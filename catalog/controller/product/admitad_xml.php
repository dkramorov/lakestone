<?php
class ControllerProductAdmitadXml extends Controller {
	public function index() {
	

		$XML_str = $this->cache->get('admitad_xml');
#		$XML_str = false;

		if (!$XML_str) {

			$XML = new DOMDocument('1.0', 'utf-8');
			$XRoot = $XML->createElement('yml_catalog');
			$XDate = $XML->createAttribute('date');
			$XDate->value=strftime('%Y-%m-%d %H:%M');
//			$XDate->value=strftime('%Y-%m-%d %H:%M:%S');
			$XRoot->appendChild($XDate);

			$XShop = $XML->createElement('shop');
			$XCategories = $XML->createElement('categories');
			$XOffers = $XML->createElement('offers');
			$XCurrencies = $XML->createElement('currencies');
			
			$XShop->appendChild($XML->createElement('name', $this->config->get('config_name')));
			$XShop->appendChild($XML->createElement('company', $this->config->get('config_owner')));
			$XShop->appendChild($XML->createElement('url', $this->config->get('config_url')));
			
			
			$XCurRUR = $XML->createElement('currency');
			$XCurRUR_id = $XML->createAttribute('id');
			$XCurRUR_rate = $XML->createAttribute('rate');
			$XCurRUR_id->value = 'RUR';
			$XCurRUR_rate->value = 1;
			$XCurRUR->appendChild($XCurRUR_id);
			$XCurRUR->appendChild($XCurRUR_rate);
			$XCurrencies->appendChild($XCurRUR);

			$this->load->model('catalog/category');

			$this->load->model('catalog/product');

			$this->load->model('tool/image');

			foreach ( $this->model_catalog_category->getCategories() as $category ) {

				$XCat = $XML->createElement('category', $category['name']);
				$XCat_id = $XML->createAttribute('id');
				$XCat_id->value = $category['category_id'];
				if ( $category['parent_id'] > 0 ) {
					$XCat_pid = $XML->createAttribute('parentId');
					$XCat_pid->value = $category['parent_id'];
					$XCat->appendChild($XCat_pid);
				}
				$XCat->appendChild($XCat_id);
				$XCategories->appendChild($XCat);

				foreach ($this->model_catalog_product->getProducts(array('filter_category_id' => $category['category_id'])) as $product) {
					//var_dump($product);
					if ($product['quantity'] > 0)
						$available = 'true';
					else
						$available = 'false';
					$XOffer = $XML->createElement('offer');
					$XOffer_id = $XML->createAttribute('id');
					$XOffer_id->value = $product['product_id'];
					$XOffer->appendChild($XOffer_id);
					$XOffer->appendChild($XML->createElement('categoryId', $category['category_id']));
					$XOffer->appendChild($XML->createElement('name', $product['name']));
					$XOffer->appendChild($XML->createElement('description', htmlspecialchars(str_replace("\n", ' ', $product['description']))));
					$XOffer->appendChild($XML->createElement('vendor', '"Lakestone"'));
					$XOffer->appendChild($XML->createElement('currencyId', 'RUR'));
					$XOffer->appendChild($XML->createElement('price', sprintf('%d', $product['price'])));
					$XOffer->appendChild($XML->createElement('available', $available));
					$XOffer->appendChild($XML->createElement('url', $this->url->link('product/product', 'product_id=' . $product['product_id'])));
					$XOffer->appendChild($XML->createElement('picture',$this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'))));
					$XOffers->appendChild($XOffer);
				}
				
			}
			
			$XShop->appendChild($XCurrencies);
			$XShop->appendChild($XCategories);
			$XShop->appendChild($XOffers);
			$XRoot->appendChild($XShop);
			$XML->appendChild($XRoot);

			$XML_str = $XML->saveXML();
			$this->cache->set('admitad_xml', $XML_str);

		}
		
		$this->response->addHeader('Content-Type: text/xml');
                $this->response->setOutput($XML_str);
		
	}
}
