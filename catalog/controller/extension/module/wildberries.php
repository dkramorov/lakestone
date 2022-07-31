<?php

class ControllerExtensionModuleWildberries extends Controller
{
    public function index($setting)
    {

    }

    /**
     * Запускает синхронизацию цен и остатков с Wildberries
     */
    public function sync ()
    {
        $this->log->write('wildberries sync stock and prices has started');

        $this->load->model('setting/setting');
        $this->load->model('extension/module/wildberries');
        $this->model_extension_module_wildberries->sync();

        $this->log->write('wildberries sync stock and prices has finished');
    }

    /**
     * Выгружает заказы из Wildberries в RetailCrm
     */
    public function export ()
    {
        $this->log->write('wildberries export orders to rcrm has started');

        $this->load->model('setting/setting');
        $this->load->model('extension/module/wildberries');
        $this->model_extension_module_wildberries->exportOrderToRcrm();

        $this->log->write('wildberries export orders to rcrm has finished');
    }

    /**
     * Обновляет статусы заказов из Wildberries в RetailCrm
     * Касается только заказов со статустом "Доставлен" и "Возврат"
     */
    public function updateOrderStatus ()
    {
        $this->log->write('wildberries update orders status to rcrm has started');

        $this->load->model('setting/setting');
        $this->load->model('extension/module/wildberries');
        $this->model_extension_module_wildberries->updateOrderStatusRetailCrm();

        $this->log->write('wildberries update orders status to rcrm has finished');
    }
}