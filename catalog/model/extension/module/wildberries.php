<?php

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\Date;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder as CreateOrder;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\{
    Address,
    Contragent,
    Customer,
    Delivery,
    Order as CreateOrderOrder,
    Payment
};

use Lakestone\SubCommon\Structures\RetailCrm\EditOrder as EditOrder;
use Lakestone\SubCommon\Structures\RetailCrm\EditOrder\ {
    Order as EditOrderOrder,
};

use Lakestone\SubCommon\Service\Logging;

class ModelExtensionModuleWildberries extends Model {

    const CONNECTION_TIMEOUT = 10;
    const PRICE_MARKUP = 67; // наценка в % на оптовую цену согласно https://lakestone.bitrix24.ru/extranet/contacts/personal/user/61/tasks/task/view/1157/

    const BRAND_BW = 'BLACKWOOD';
    const BRAND_LK = 'LAKESTONE';

    const STATUS_ID_REFUND_WB = 3;
    const STATUS_ID_DELIVERED_WB = 2;
    const STATUS_ID_CLIENT_CANCEL_WB = 1;

    public $log = false;
    public $cache = false;

    protected $lkProductsList = [];
    protected $wbProductsList = [];

    private $CURL;

    public function __construct($params)
    {
        parent::__construct($params);
        $this->log = new Log('wildberries.log');
        $this->cache = new Cache('file', 3600 * 1);

        /*try {
            $this->lkProductsList = $this->getLKProductsList();
        }  catch (\Exception $e) {
            $error = $e->getMessage();
            $this->log->write('get lk product list error: ' + $error);
            throw new \Exception("Error on update prices: " . $error);
        }

        try {
            $this->wbProductsList = $this->getWBProductsList();
        }  catch (\Exception $e) {
            $error = $e->getMessage();
            $this->log->write('get wb product list error: ' + $error);
            throw new \Exception("Error on update prices: " . $error);
        }*/
    }

    /**
     * @param $type
     * @param array $params
     * @return mixed|null
     */
    public function request($type, $params = [], $method = 'post')
    {
        $requestUrl = implode('', array(
            WILDBERRIES_API_HOST,
            $type
        ));

        $this->curl = new Curl($requestUrl, true, self::CONNECTION_TIMEOUT);

        $this->curl->httpheader = [
            "Content-Type:application/json",
            "Authorization:" . WILDBERRIES_API_TOKEN,
        ];

        if ($method == 'post') {
            $this->curl->setPostPramas(json_encode($params));
        } elseif ($method == 'get') {
            $this->curl->setGetPramas($params);
        }

        $this->curl->createCurl();

        $result = $this->curl->getContentJsonDecode();

        return $result;
    }

    /**
     * Возвращает список заведенных товаров на wildberries
     * @return array
     */
    public function getWBProductsList()
    {
        $allItems = [];

        $total = 0;
        $limit = 100;
        $ofset = 0;

        $type = '/card/list';

        $i = 1;
        $lost = $limit;

        while ($lost) {
            $params = [
                "jsonrpc" => "2.0",
                "params" => [
                    "query" => [
                        "limit" => $limit,
                        "offset" => $ofset,
                        "total" => $total,
                    ]
                ]
            ];

            $result = $this->request($type, $params);

            $items = $result['result']['cards'];
            $count = $result['result']['cursor']['total'];
            $ofset = $i * $limit;
            $lost = $count - $ofset;

            if (count($items) > 0) {
                $allItems = array_merge($allItems, $items);
            } else {
                $lost = 0;
            }

            $i++;
        }

        //$this->cache->set('wildberries_products_list', true);

        return $allItems;
    }

    /**
     * Возвращает список товаров в БД Lakestone (товары lakestone и blackwood)
     * @return array
     */
    public function getLKProductsList ()
    {
        $this->log->write('wildberries get lk items list has started');
        $this->load->model('catalog/product');
        $this->load->model('extension/module/integration');

        $Now = new DateTime();
        $arProducts =  [];
        $filter_data = [];

        $this->lkProducts = $this->model_catalog_product->getProducts($filter_data);

        foreach ($this->model_catalog_product->getProducts() as $product) {
            $product['brand'] = self::BRAND_LK;
            $arProducts[] = $product;
        }

        # подбираем товары BLACKWOOD в общую копилку
        $variants_integration = $this->model_extension_module_integration->findIntegrationVariant([]);

        if (eRU($variants_integration)) {
            foreach ($variants_integration as $variant) {
                if (strpos($variant['name'], self::BRAND_BW) !== false) {
                    $variant['model'] = $variant['code'];
                    $variant['brand'] = self::BRAND_BW;
                    $arProducts[] = $variant;
                }
            }
        }

        $this->log->write('wildberries get lk items list has finished');

        return $arProducts;
    }

    /**
     * Обновляет остатки товаро в Wildberries путем выгрузки остатка из БД lakestone
     * @return mixed|null
     * @throws Exception
     */
    public function updateStocks ()
    {
        $this->log->write('wildberries update stocks has started');

        $wbItemStocks = [];
        $lkItemStocks = [];

        $type = '/api/v2/stocks';
        $lkProductsList = $this->getLKProductsList();
        $wbProductsList = $this->getWBProductsList();

        if (eRU($wbProductsList) && eRU($lkProductsList)) {
            foreach ($lkProductsList as $arProduct) {
                $quantity = (int) $arProduct['quantity'];
                $barcode = $arProduct['model'];
                $lkItemStocks[$barcode] = $quantity;
            }

            foreach ($wbProductsList as $arWbProduct) {

                if (eRU($arWbProduct['addin'])) {
                    foreach ($arWbProduct['addin'] as $prop) {
                        if ($prop['type'] == 'Бренд') {
                            $brand = strtoupper(array_shift($prop['params'])['value']);
                            break;
                        }
                    }
                }

                if (eRU($arWbProduct['nomenclatures'])) {
                    foreach ($arWbProduct['nomenclatures'] as $arWrProductNomenclature) {
                        $barcode = $arWrProductNomenclature['variations'][0]['barcodes'][0];
                        $supplierVendorCode = $arWbProduct['supplierVendorCode'];
                        $vendorCode = $arWrProductNomenclature['vendorCode'];

                        if ($brand == self::BRAND_BW) {
                            $model = str_replace(['/', '', ' ', PHP_EOL,], [''], $supplierVendorCode);
                        } else {
                            $model = $supplierVendorCode && $vendorCode ?
                                $supplierVendorCode . $vendorCode :
                                ($supplierVendorCode ?: $vendorCode);
                        }

                        $quantity = isset($lkItemStocks[$model]) ? ($lkItemStocks[$model] > 1 ? $lkItemStocks[$model] : 0) : 0;

                        $wbItemStock = [
                            "barcode" => $barcode,
                            "stock" => (int) $quantity,
                            "warehouseId" => (int) WILDBERRIES_WAREHOUSE_ID,
                        ];

                        $wbItemStockCsv = array_merge(["model" => $model, "brand" => $brand], $wbItemStock);

                        $wbItemStocksCsv[] = $wbItemStockCsv;
                        $wbItemStocks[] = $wbItemStock;
                    }
                }
            }

            if (eRU($wbItemStocks)) {
                getCsv([
                    'COLUMNS' => ['model', 'brand', 'barcode', 'stock', 'warehouseid'],
                    'VALUES' => $wbItemStocksCsv
                ], DIR_LOGS . 'update.stocks.csv');

                try {
                    $result = $this->request($type, $wbItemStocks);
                }  catch (\Exception $e) {
                    $error = $e->getMessage();
                    $this->log->write('wildberries update stocks: ' + $error);
                    throw new \Exception("Error on update stocks: " . $error);
                }
            }
        }

        $this->log->write('wildberries update stocks has finished');

        return $result;
    }

    /**
     * Обновляет цены товаров, заведённых на Wildberries путем выгрузки цен из БД lakestone
     * @return mixed|null
     * @throws Exception
     */
    public function updatePrices ()
    {
        $this->log->write('wildberries update prices has started');

        $wbItemPrices = [];
        $lkItemPrices = [];

        $type = '/public/api/v1/prices';
        $lkProductsList = $this->getLKProductsList();
        $wbProductsList = $this->getWBProductsList();
        
        if (eRU($wbProductsList) && eRU($lkProductsList)) {
            foreach ($lkProductsList as $arProduct) {
                $priceLk = (int) $arProduct['price'];
                $priceWB = $priceLk ? (int) ($priceLk * (1 + self::PRICE_MARKUP / 100)) : false;
                if (!empty($priceWB)) {
                    $lkItemPrices[$arProduct['model']] = $priceWB; // todo Уточнить тут как получить штрихкод аналогичный со штрихкодом в ягодах
                }
            }

            foreach ($wbProductsList as $arWbProduct) {

                if (eRU($arWbProduct['addin'])) {
                    foreach ($arWbProduct['addin'] as $prop) {
                        if ($prop['type'] == 'Бренд') {
                            $brand = strtoupper(array_shift($prop['params'])['value']);
                            break;
                        }
                    }
                }

                if (eRU($arWbProduct['nomenclatures'])) {
                    foreach ($arWbProduct['nomenclatures'] as $arWrProductNomenclature) {
                        $nmId = $arWrProductNomenclature['nmId'];
                        $barcode = $arWrProductNomenclature['variations'][0]['barcodes'][0];
                        $supplierVendorCode = $arWbProduct['supplierVendorCode'];
                        $vendorCode = $arWrProductNomenclature['vendorCode'];

                        if ($brand == self::BRAND_BW) {
                            $model = str_replace(['/', '', ' ', PHP_EOL,], [''], $supplierVendorCode);
                        } else {
                            $model = $supplierVendorCode && $vendorCode ?
                                $supplierVendorCode . $vendorCode :
                                ($supplierVendorCode ?: $vendorCode);
                        }

                        $priceWB = $lkItemPrices[$model] ?? false;

                        if ($priceWB) {

                            $wbItemPrice = [
                                "nmId" => $nmId,
                                "price" => $priceWB,
                            ];

                            $wbItemPriceCsv = array_merge(['model' => $model, 'barcode' => $barcode, 'brand' => $brand], $wbItemPrice);

                            $wbItemPricesCsv[] = $wbItemPriceCsv;
                            $wbItemPrices[] = $wbItemPrice;
                        }
                    }
                }
            }

            if (eRU($wbItemPrices)) {
                getCsv([
                    'COLUMNS' => ['model', 'barcode', 'brand', 'nmid', 'price'],
                    'VALUES' => $wbItemPricesCsv
                ], DIR_LOGS . 'update.prices.csv');

                try {
                    $result = $this->request($type, $wbItemPrices);
                }  catch (\Exception $e) {
                    $error = $e->getMessage();
                    $this->log->write('wildberries update prices: ' + $error);
                    throw new \Exception("Error on update prices: " . $error);
                }
            }
        }

        $this->log->write('wildberries update prices has finished');

        return $result;
    }

    /**
     * Обновляет заказ Wildberries в админке Lakestone
     * @param string $id
     * @param array $data
     */
    public function updateWBLKOrder(string $id, array $data)
    {
        $sql = "";
        foreach ($data as $key => $val) {
            $sql .= "`$key` = '" . $this->db->escape($val) . "',";
        }
        $sql = substr($sql, 0, -1);
        $this->db->query("INSERT INTO `wildberries_order` SET " .
            "`id` = '" . $this->db->escape($id) . "', " .
            $sql .
            " ON DUPLICATE KEY UPDATE " .
            $sql
        );
    }

    /**
     * Возвращает список заказов за последние $duration минут
     * @return mixed
     */
    public function getWBOrders (string $period, int $limit = 100): ?array
    {
        $orders = [];
        $skip = 0;
        $type = '/api/v2/orders';
        $dateFormat = 'Y-m-d\TH:i:s.u\Z';
        $params = [
            "date_start" => (new DateTime($period))
                ->setTimezone(new DateTimeZone('UTC'))
                ->format($dateFormat),
            "take" => $limit,
            "skip" => $skip
        ];

        $result = $this->request($type, $params, 'get');
        $orders = $result['orders'];
        $count = $result['total'];

        return $orders;
    }

    /**
     * Возвращает сохраненные заказы WB на сайте LK (чтобы исключить повторную передачу заказов в RCRM)
     * @param string $id
     * @return array
     */
    public function getWBLKOrders (string $id = '')
    {
        if (!empty($id)) {
            $query = "SELECT * FROM `wildberries_order` WHERE id = '". $this->db->escape($id) ."'";
        } else {
            $query = "SELECT * FROM `wildberries_order`";
        }

        $res = $this->db->query($query);
        $orders = $id ? $res->row : $res->rows;

        return $orders;
    }

    /**
     * Возвращает массив расшифровок статусов заказа Wildberries
     * @return string[]
     */
    public function getWBOrderStatuses ()
    {
        $statuses = [
            'delivery' => [
                0 => 'Новый заказ',
                1 => 'Принял заказ',
                2 => 'Сборочное задание завершено',
                3 => 'Сборочное задание отклонено',
                4 => '',
                5 => 'На доставке курьером',
                6 => 'Курьер довез и клиент принял товар',
                7 => 'Клиент не принял товар',
            ],
            'client' => [
                1 => 'Отмена клиента',
                2 => 'Доставлен',
                3 => 'Возврат',
                4 => 'Ожидает',
                5 => 'Брак',
            ]
        ];

        return $statuses;
    }

    /**
     * Возвращает параметры товара из Wildberries для записи в заказ в RCRM
     * @param $order
     * @return array|false
     */
    public function getWBProductToRcrm ($order)
    {
        $this->wbProductsList = eRU($this->wbProductsList) ? $this->wbProductsList : $this->getWBProductsList();

        foreach ($this->wbProductsList as $arWbProduct) {

            $model = '';

            if (eRU($arWbProduct['addin'])) {
                foreach ($arWbProduct['addin'] as $prop) {
                    if ($prop['type'] == 'Бренд') {
                        $brand = strtoupper(array_shift($prop['params'])['value']);
                        break;
                    }
                }
            }

            if (eRU($arWbProduct['nomenclatures'])) {
                foreach ($arWbProduct['nomenclatures'] as $arWrProductNomenclature) {
                    $nmId = $arWrProductNomenclature['nmId'];
                    $barcode = $arWrProductNomenclature['variations'][0]['barcodes'][0];
                    $supplierVendorCode = $arWbProduct['supplierVendorCode'];
                    $vendorCode = $arWrProductNomenclature['vendorCode'];

                    if ($brand == self::BRAND_BW) {
                        $model = str_replace(['/', '', ' ', PHP_EOL,], [''], $supplierVendorCode);
                        $shop = 'www-blackwoodbag-ru';
                    } else {
                        $model = $supplierVendorCode && $vendorCode ?
                            $supplierVendorCode . $vendorCode :
                            ($supplierVendorCode ?: $vendorCode);
                        $shop = 'www-lakestone-ru';
                    }

                    if ($barcode == $order['barcode']) {
                        $product = [
                            'price' => $order['totalPrice'] / 100,
                            'quantity' => 1,
                            'model' => $model,
                            'name' => $model,
                            'product_id' => $nmId,
                            'shop' => $shop,
                        ];

                        return $product;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Преобразует дату/время wb в дату\время Москвы
     * @param $date
     * @return string
     * @throws Exception
     */
    protected function wbDateToSiteDate (string $date): \DateTime
    {
        $dt = new DateTime($date, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Moscow'));
        return $dt;
    }

    /**
     * Экспорт заказа Wildberries в RetailCRM
     */
    public function exportOrderToRcrm ()
    {
        set_time_limit(0);
        ignore_user_abort(TRUE);

        $this->cache->set('WildberriesOrderSyncLock', true);
        //$Log = new Log('wildberries.order.log');
        //$Log->write('Wildberries export orders has started');

        Logging::info('Wildberries export orders has started');

        $this->load->model('extension/module/retailcrm');
        $this->load->model('tool/status');

        $wbOrderStatuses = $this->getWBOrderStatuses();
        $this->wbProductsList = eRU($this->wbProductsList) ? $this->wbProductsList : $this->getWBProductsList();

        $counter = 0;
        $wbOrdersPeriod = '-3 hour';

        $wbOrders = $this->getWBOrders($wbOrdersPeriod);

        if (eRU($wbOrders)) {

            foreach ($wbOrders as $order) {

                //$Log->write('Got Wildberries order:' . $order['orderId']);
                Logging::info('Got Wildberries order', ['order_id' => $order['orderId']]);
                $myOrder = $this->getWBLKOrders($order['orderId']);

                if (!empty($myOrder)) {
                    /*$Log->write(sprintf(
                        "already registered in my db: %s (%s)",
                        $myOrder['crm_id'] ?? '-',
                        $myOrder['crm_site'] ?? '-'
                    ));*/

                    Logging::warning('Already registered in site db', ['crm_id' => $myOrder['crm_id'] ?? '-', 'crm_site' => $myOrder['crm_site'] ?? '-']);

                    continue;
                }

                $orderStatus = !empty($order['status']) ? ($wbOrderStatuses['delivery'][$order['status']] ?: $order['status']) : 'empty';
                $rcrm_order = $this->model_extension_module_retailcrm->getOrders(['customFields' => [RetailCrmInterface::customerFieldWildberriesOrderId => $order['orderId']]]);

                if (!empty($rcrm_order)) {

                    /*$Log->write(sprintf(
                        'already registered in RCRM: %s (%s)',
                        $rcrm_order[0]->number ?? '-',
                        $rcrm_order[0]->site ?? '-'
                    ));*/

                    Logging::warning('Already registered in RCRM', ['crm_id' => $rcrm_order[0]->number ?? '-', 'crm_site' => $rcrm_order[0]->site ?? '-']);

                    $this->updateWBLKOrder($order['orderId'], [
                        'status' => $orderStatus,
                        'wildberries_date' => $order['dateCreated'],
                        'crm_id' => $rcrm_order[0]->number,
                        'crm_date' => $rcrm_order[0]->createdAt,
                        'crm_site' => $rcrm_order[0]->site,
                    ]);
                    continue;
                }

                //$Log->write("Creating new to RCRM");
                Logging::info('Creating new to RCRM');

                preg_match('#((г|г\.)\s)?([А-Яа-я-]+)(.*,)?\s.*#iu', $order['officeAddress'], $matchesCity);
                $order['city'] = $matchesCity[3] ?? '';
                $amount = 0;

                $userAddressArr = $order['deliveryAddressDetails'];
                $userAddress =
                    'Адрес покупателя: '
                    . ($userAddressArr['province'] ?: '')
                    . ($userAddressArr['area'] && $userAddressArr['area'] != $userAddressArr['city'] ? ', ' . $userAddressArr['area'] : '')
                    . ($userAddressArr['city'] ? ', ' . $userAddressArr['city'] : '')
                    . ($userAddressArr['home'] ? ', ' . $userAddressArr['home'] : '')
                    . ($userAddressArr['flat'] ? ', ' . $userAddressArr['flat'] : '')
                    . ($userAddressArr['entrance'] ? ', ' . $userAddressArr['entrance'] : '');


                $newOrder = new CreateOrderOrder();
                $createOrder = new CreateOrder();

                $product = $this->getWBProductToRcrm($order);

                $amount += (float)($product['price'] * $product['quantity']);

                $site = $product['shop'];

                $newItem = $this->model_extension_module_retailcrm->createOrderItemByProductCode($product['model']);

                //echo get_class($newItem);
                //echo '#######';

                $newItem
                    ->setInitialPrice((float)$product['price'])
                    ->setQuantity((int)$product['quantity']);

                $newOrder
                    ->addItem($newItem)
                    ->setContragent(
                        (new Contragent())
                            ->setContragentType(RetailCrmInterface::contragentTypeLegalEntity)
                            ->setLegalName('ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ "ВАЙЛДБЕРРИЗ"')
                            ->setLegalAddress('142717, Московская обл, Ленинский р-н, деревня Мильково, влд 1')
                            ->setINN('7721546864')
                            ->setKPP('500301001')
                            ->setOGRN('1067746062449')
                    )
                    ->addPayment(
                        (new Payment())
                            ->setStatus(RetailCrmInterface::paymentStatusNotPaid)
                            ->setType(RetailCrmInterface::paymentByBank)
                            ->setAmount((float)$amount * 0.95)
                    )
                    ->setCustomer(
                        (new Customer())
                            ->setId(7213)
                    )
                    ->setDelivery(
                        (new Delivery())
                            ->setAddress(
                                (new Address())
                                    ->setCity($order['city'] ?? 'Не определено')
                                    ->setText($order['officeAddress'])
                            )
                            //->setDate(new Date($order['shipment_date']))
                    )
                    ->setStatus(RetailCrmInterface::orderStatusNewMP)
                    ->setOrderType(RetailCrmInterface::orderTypeMarketplace)
                    ->setOrderMethod(RetailCrmInterface::orderMethodPhone)
                    ->setDiscountManualPercent(5)
                    ->setCreatedAt($this->wbDateToSiteDate($order['dateCreated']))
                    ->setFirstName('Wildberries')
                    ->setLastName($order['userInfo']['fio'])
                    ->setPhone($order['userInfo']['phone'])
                    ->setManagerComment($order['orderId'])
                    ->setCustomerComment($userAddress)
                    ->addCustomField(RetailCrmInterface::customerFieldWildberriesOrderId, $order['orderId'])
                ;

                $createOrder
                    ->setOrder($newOrder)
                    ->setSite($site)
                ;

                //$res = $this->model_extension_module_retailcrm->sendOrder($createOrder);

                /***************************************************/
                /***************************************************/
                /*if ($res->success) {
                    $this->updateWBLKOrder($order['orderId'], [
                        'status' => $orderStatus,
                        'wildberries_date' => $order['dateCreated'],
                        'crm_id' => $res->order->number,
                        'crm_date' => $res->order->createdAt,
                        'crm_site' => $res->order->site,
                    ]);
                }*/
                $counter++;
            }
        } else {
            //$Log->write('Noone orders or error on wildberries server');
            Logging::notice('Noone orders or error on wildberries server');
        }

        Logging::info('Wildberries updater has finished');
        //$Log->write('Wildberries updater has finished');
        $this->cache->set('WildberriesOrderSyncLock', false);
        $this->model_tool_status->done('wildberries_order', 1, $counter);
    }

    /**
     * Обновляет в retailcrm статус заказа для заказов wildberries со статусом "Возврат" и "Доставлен"
     * @return int
     */
    public function updateOrderStatusRetailCrm (): int
    {
        set_time_limit(0);
        ignore_user_abort(TRUE);

        $this->cache->set('WildberriesOrderStatusSyncLock', true);
        $Log = new Log('wildberries.order.log');

        $Log->write('Wildberries update orders status has started');

        $this->load->model('extension/module/retailcrm');
        $this->load->model('tool/status');

        $wbOrderStatuses = $this->getWBOrderStatuses();

        $wbOrdersPeriod = '-1 month';
        $wbOrdersLimit = 1000;

        $counter = 0;

        $wbOrders = $this->getWBOrders($wbOrdersPeriod, $wbOrdersLimit);

        if (eRU($wbOrders)) {

            foreach ($wbOrders as $order) {

                $Log->write('Got Wildberries order:' . $order['orderId'] . ', date: ' . $order['dateCreated']);

                $myOrder = $this->getWBLKOrders($order['orderId']);
                $orderStatus = !empty($order['status']) ? ($wbOrderStatuses['delivery'][$order['status']] ?: $order['status']) : 'empty';
                $orderUserStatus = !empty($order['userStatus']) ? ($wbOrderStatuses['client'][$order['userStatus']] ?: $order['userStatus']) : 'empty';

                # Обрабатываем только статус "Возврат" и "Доставлен" и "Отмена клиента"
                if (!in_array($order['userStatus'], [self::STATUS_ID_DELIVERED_WB, self::STATUS_ID_REFUND_WB, self::STATUS_ID_CLIENT_CANCEL_WB])) {
                    continue;
                }

                if (empty($myOrder)) {
                    $Log->write(sprintf("not registered in lk db: %s (%s)", $myOrder['crm_id'] ?? '-', $myOrder['crm_site'] ?? '-'));
                    continue;
                }

                $rcrm_order = $this->model_extension_module_retailcrm->getOrders(['customFields' => [RetailCrmInterface::customerFieldWildberriesOrderId => $order['orderId']]]);

                if (empty($rcrm_order)) {
                    $Log->write(sprintf('not registered in RCRM: %s (%s)', $rcrm_order[0]->number ?? '-', $rcrm_order[0]->site ?? '-'));
                    continue;
                } elseif (in_array($rcrm_order[0]->status, [
                    RetailCrmInterface::orderStatusDublicate,
                    RetailCrmInterface::orderStatusDeliveryCallFailed,
                    RetailCrmInterface::orderStatusNotFound,
                    RetailCrmInterface::orderStatusCanceledBeforeDelivered,
                    RetailCrmInterface::orderStatusCanceledInDelivered,
                    RetailCrmInterface::orderStatusReturnedInThirtyDays
                ])) {
                    $Log->write(sprintf('status "%s" in exception list to cancel update order status in RetailCrm (order %s)', $rcrm_order[0]->status, $rcrm_order[0]->number));
                    continue;
                }

                $newOrder = new EditOrderOrder();
                $editOrder = new EditOrder();

                if ($order['userStatus'] == self::STATUS_ID_DELIVERED_WB) {
                    if ($rcrm_order[0]->status == RetailCrmInterface::orderStatusCompleted) {
                        $Log->write(sprintf('the same order statuses (%s) in Wildberries and RetailCrm (order %s)', $rcrm_order[0]->number ?? '-', $rcrm_order[0]->number ?? '-'));
                        continue;
                    } else {
                        $orderRcrmStatus = RetailCrmInterface::orderStatusCompleted;
                    }
                } elseif (in_array($order['userStatus'], [self::STATUS_ID_REFUND_WB, self::STATUS_ID_CLIENT_CANCEL_WB])) {
                    if ($rcrm_order[0]->status == RetailCrmInterface::orderStatusNaSkladWb) {
                        $Log->write(sprintf('the same order statuses (%s) in Wildberries and RetailCrm (order %s)', $rcrm_order[0]->number ?? '-', $rcrm_order[0]->number ?? '-'));
                        continue;
                    } else {
                        $orderRcrmStatus = RetailCrmInterface::orderStatusNaSkladWb;
                    }
                }

                $newOrder
                    ->setStatus($orderRcrmStatus)
                    ->setOrderId(intval($myOrder['crm_id']));

                $editOrder
                    ->setOrder($newOrder)
                    ->setSite($myOrder['crm_site'])
                    ->setOrderEditBy('id')
                ;

                /*save2FileVariable([
                    'order id wb: ' => $order['orderId'],
                    'order id rc: ' => $myOrder['crm_id'],
                    'site' => $myOrder['crm_site'],
                    'status' => $orderRcrmStatus,
                ], 'update.order.status.txt', FILE_APPEND);*/

                $res = $this->model_extension_module_retailcrm->sendOrder($editOrder, 'orders/' . intval($myOrder['crm_id']) . '/edit');

                if ($res->success) {
                    $this->updateWBLKOrder($order['orderId'], [
                        'status' => $orderStatus,
                        'user_status' => $orderUserStatus,
                        'wildberries_date' => (new \DateTime())->format('Y-m-d H:i:s'),
                    ]);

                    $Log->write('update order ' . $myOrder['crm_id'] . ' set status ' . $orderRcrmStatus);
                }

                $counter++;
            }
        } else {
            $Log->write('Noone orders or error on wildberries server');
        }

        $Log->write('Wildberries update orders status has finished');
        $this->cache->set('WildberriesOrderStatusSyncLock', false);
        $this->model_tool_status->done('wildberries_order', 1, $counter);

        return $counter;
    }

    public function sync ()
    {
        $this->updatePrices();
        $this->updateStocks();
    }
}
