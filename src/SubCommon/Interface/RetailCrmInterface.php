<?php

namespace Lakestone\SubCommon\Interface;

interface RetailCrmInterface {
  
  /**
   * API
   */
  const repeatRequestAttempt = 5;
  const repeatRequestTimeout = 1; // seconds
  
  /**
   * The site identifications MUST be with prefix "site"
   */
  const siteLakestone = 'www-lakestone-ru';
  const siteBlackwood = 'www-blackwoodbag-ru';
  
  /**
   * payment types
   */
  const paymentByCard = 'bank-card';
  const paymentByCash = 'cash';
  const paymentByBank = 'bank-transfer';
  const paymentByCashOffice = 'kassa';
  
  /**
   * payment statuses
   */
  const paymentStatusNotPaid = 'not-paid'; // Не оплачен
  const paymentStatusPaid = 'paid'; // Оплачен
  const paymentStatusFail = 'fail'; // Ошибка
  const paymentStatusInvoice = 'invoice'; // Выставлен счет
  const paymentStatusStart = 'payment-start'; // Платеж проведен
  const paymentStatusCreditCheck = 'credit-check'; // Проверка документов на кредит
  const paymentStatusCreditApproved = 'credit-approved'; // Кредит одобрен
  
  /**
   * price types
   */
  const priceTypeSale = 'sale'; // Распродажа розничная
  const priceTypeSaleWholesale = 'sale_wholesale'; // Распродажа оптовая
  const priceTypeRegular = 'base'; // Розничная
  const priceTypeWholesale = 'wholesale'; // Оптовая
  
  /**
   * Contragent types
   */
  const contragentTypeIndividual = 'individual'; // физическое лицо
  const contragentTypeLegalEntity = 'legal-entity'; // юридическое лицо
  const contragentTypeEnterpreneur = 'enterpreneur'; // индивидуальный предприниматель
  
  /**
   * order status
   */
  const orderStatusPreOrder = 'predzakaz'; // Предзаказ
  const orderStatusNew = 'new'; // Новый
  const orderStatusSend2Assembling = 'send-to-assembling'; // Передано в комплектацию
  const orderStatusCompleted = 'complete'; // Выполнен
  const orderStatusDelivering = 'delivering'; // Доставляется
  const orderStatusClientConfirming = 'client-confirmed'; // Согласование с клиентом
  const orderStatusAssembling = 'assembling'; // Комплектуется
  const orderStatusOfferAnalog = 'offer-analog'; // Предложить замену
  const orderStatusDelivered2PickPoint = 'postupilo-v-pvz'; // Поступило в пункт выдачи
  const orderStatusAssemblingComplete = 'assembling-complete'; // Укомплектован
  const orderStatusAvailabilityConfirmed = 'availability-confirmed'; // Наличие подтверждено
  const orderStatusCallFailed = 'nedozvon'; // Недозвон
  const orderStatusReturned = 'vozvrat'; // Возврат
  const orderStatusToReorder = 'rasform-zakaz'; // Расформировать заказ
  const orderStatusNewMP = 'new-mp'; // Новый МП
  const orderStatusPostReserved = 'soglas-pod-zakaz'; // Согласование ПОД ЗАКАЗ
  const orderStatusInProceed4Reserved = 'prepayed'; // В производстве ПОД ЗАКАЗ
  const orderStatusNotEnough = 'ne-hvataet'; // Не хватает товара
  const orderStatusDublicate = 'duble'; // Дубль заказа
  const orderStatusDeliveryCallFailed = 'no-call'; // Недозвон до доставки
  const orderStatusNotFound = 'no-product'; // Нет в наличии
  const orderStatusCanceledBeforeDelivered = 'cancel-other'; // Отменен до доставки
  const orderStatusCanceledInDelivered = 'neystroil'; // Отменен во время доставки
  const orderStatusReturnedInThirtyDays = 'prices-did-not-suit'; // Возврат в течение 30 дней
  const orderStatusOffice = 'ofice'; // Офис
  const orderStatusNaSkladWb = 'na-sklad-vb'; // Возврат товара на склад ВБ
  const orderStatusVozvratOzon = 'vozvrat-ozon'; // Возврат Озон

  /**
   * order types
   */
  const orderTypeEshopLegal = 'eshop-legal'; // Интернет-магазин
  const orderTypeCorporationClient = 'corp'; // Корпоративный клиент
  const orderTypeMarketplace = 'marketplace'; // Маркетплейс
  const orderTypeWholesaleCustomer = 'opt'; // Оптовый клиент
  const orderTypeAdvertising = 'rk'; // Рекламная компания
  const orderTypeJointPurchases = 'sp'; // Совместные покупки
  const orderTypeEshopIndividual = 'eshop-individual'; // Физическое лицо
  const orderTypeLegalEntity = 'urlico'; // Юридическое лицо
  
  /**
   * order methods
   */
  const orderMethodPhone = 'phone'; // По телефону
  const orderMethodShoppingCart = 'shopping-cart'; // Через корзину
  const orderMethodOneClick = 'one-click'; // В один клик
  const orderMethodPriceDecreaseRequest = 'price-decrease-request'; // Запрос на понижение цены
  const orderMethodLandingPage = 'landing-page'; // Заявка с посадочной страницы
  const orderMethodOffline = 'offline'; // Оффлайн
  const orderMethodMobileApp = 'app'; // Мобильное приложение
  const orderMethodLiveChat = 'live-chat'; // Онлайн-консультант
  const orderMethodTerminal = 'terminal'; // Терминал
  const orderMethodYandexMarket = 'market'; // Яндекс Маркет
  const orderMethodMessenger = 'messenger'; // Мессенджеры
  const orderMethodGoods = 'goods'; // Goods
  const orderMethodPreOrder = 'predzakaz'; // Предзаказ
  
  /**
   * shipping types and other
   */
  const shippingCdekProviderCode = 'sdek';
  const shippingBoxberryProviderCode = 'boxberry';
  const shippingCdekTariffCode = 136;
  const shippingBoxberryTariffCode = 'boxberry_tariff';
  
  /**
   * customer fields
   */
  const customerFieldRoistatId = 'roistat'; // Roistat ID
  const customerFieldAdditionalSales = 'dop_prodaji'; // Доппродажи
  const customerFieldMoyskladexternalid = 'moyskladexternalid'; // Идентификатор в системе МойСклад
  const customerFieldDalliCode = 'dalli_barcode'; // Код Dalli
  const customerFieldEshopOrderId = 'eshop_orderid'; // Номер заказ в интернет-магазине
  const customerFieldFormName = 'form_name'; // Форма захвата
  const customerFieldWildberriesOrderId = 'wildberries_order_id'; // Идентификатор заказа в Wildberries
  const customerFieldOzonOrderId = 'ozon_order_id'; // Идентификатор заказа в Озон

  const orderActionCreate = 'orders/create';
  const orderActionEdit = 'orders/#ORDER_ID#/edit';
}
