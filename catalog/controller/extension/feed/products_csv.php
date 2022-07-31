<?php
class ControllerExtensionFeedProductsCsv extends Controller {

  private $CSV = '';

  public function index() {

    if (empty($this->request->get['key']) or $this->request->get['key'] !== 'OpPD69Ix7CGhpZwGriHQJzX1BeMnyRC01RFtI0QS') {
      $this->response->redirect('/');
    }

    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    $filter = [
//      'limit' => 10,
    ];

    /*
     * Название товара
      Категория
      Модель (по сути это продажный артикул)
      Артикул (по сути это закупочный артикул поставщика)
      Размер
      Вес
      Цена (розничная)
      Цена оптовая
      Количество
    */
    $this->addStr('Название товара');
    $this->addStr('Категория');
    $this->addStr('Модель');
    $this->addStr('Артикул');
    $this->addStr('Размер');
    $this->addStr('Розница');
    $this->addStr('Опт');
    $this->addStr('Количество', true);
    $this->CSV .= "\n";

    foreach ($this->model_catalog_product->getProducts($filter) as $product) {

      $this->addStr($product['name']);
      $this->addStr($this->model_catalog_category->getCategory($this->model_catalog_product->getCategories($product['product_id'])[0]['category_id'])['name']);
      $this->addStr($product['model']);
      $this->addStr($product['sku']);
      $product_attributes = $this->model_catalog_product->getProductAttributes($product['product_id']);
      $attributes = [];
      foreach ($product_attributes as $attribute_group) {
        if ($attribute_group['name'] !== 'Основные') continue;
        foreach ($attribute_group['attribute'] as $attribute) {
          if ($attribute['name'] == 'Вес, грамм')
            $attributes['weight'] = $attribute['text'];
          if ($attribute['name'] == 'Внешние размеры, см')
            $attributes['size_out'] = $attribute['text'];
        }
      }
      $this->addStr($attributes['size_out']);
      $this->addStr($attributes['weight']);
      $this->addStr($product['price']);
      $this->addStr($this->model_catalog_product->getProductDiscounts($product['product_id'])[0]['price']);
      $this->addStr($product['quantity'], true);

      $this->CSV .= "\n";

    }

    $this->response->addHeader('Content-Type: text/csv');
    $this->response->setOutput($this->CSV);

  }

  private function addStr($data, $last = false) {

    $this->CSV .= '"' . str_replace('"', '\\"', $data) . '"';
    if (!$last) $this->CSV .= ',';

  }

}