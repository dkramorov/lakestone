<?php
class ControllerExtensionModulePotesua extends Controller {

	public function index($setting) {
		$this->load->language('extension/module/potesua');
        $data = array();
		return $this->load->view('extension/module/potesua', $data);
	}

    public function getCustomIcons() {
        $this->load->model('extension/module/potesua');
        $this->load->model('tool/image');
        $data = array();
	    if (isset($this->request->get['product_id'])) {
            $images = $this->model_extension_module_potesua->getCustomIcons($this->request->get['product_id']);
            $data['images'] = $images;
            $data['thumbs'] = array();
            foreach ($images as $image) {
                if ($image['image'] && file_exists(DIR_IMAGE . $image['image'])) {
                    $img_info = getimagesize(DIR_IMAGE . $image['image']);
                    $data['thumbs'][] = array(
                        'image' => $image['image'],
                        'preview' => $this->model_tool_image->resize($image['image'], 150, 150),
                        'full' => HTTPS_CATALOG . 'image/' . $image['image'],
                        'width' => $img_info[0],
                        'height' => $img_info[1],
                        'name' => $image['name'],
                    );
                }
            }

        }
	    $result = $this->load->view('extension/module/potesua', $data);
        $this->response->setOutput($result);
    }
}
