<?php

class ControllerStartupLocality extends Controller {

  public function index() {
//    var_dump('startup locality');
    $this->session->data['tooltip'] = false;
//    if (isset($this->request->get['location']) and !empty($this->request->get['location'])) {
//      $loc_arr = explode(' ', $this->request->get['location']);
//      $this->session->data['Locality'] = $this->request->get['location'];
//      if (sizeof($loc_arr) > 1) {
//        $this->session->data['LocalityName'] = $loc_arr[1];
//      } else {
//        $this->session->data['LocalityName'] = $locality;
//      }
//      return;
//    }
    if (!isset($this->session->data['Locality']) or empty($this->session->data['Locality'])) {
      $this->session->data['tooltip'] = true;
      $this->load->model('localisation/locality');
      if (isset($_SERVER['HTTP_X_REAL_IP']))
        $clientIP = $_SERVER['HTTP_X_REAL_IP'];
      elseif (isset($_SERVER['REMOTE_ADDR']))
        $clientIP = $_SERVER['REMOTE_ADDR'];
      $locality = '';
      // GeoIP
      if (!empty($clientIP)) {
        $logger = new Log('geo_ip.log');
        $locality = $this->model_localisation_locality->findCity($clientIP);
        if (empty($locality)) {
          $locality = 'Москва';
          $logger->write('unknown location for IP is ' . $clientIP . ', set location is "' . $locality . '"');
        } else {
          $logger->write('set location is "' . $locality . '" for IP is ' . $clientIP);
        }
      } else {
        $locality = 'Москва';
      }
      $this->session->data['LocalityName'] = $locality;
      $this->session->data['Locality'] = 'г. ' . $locality;
    }
    // Setup SubDomains
    if (file_exists('config_sub.php')) include('config_sub.php');
    $SpecialLocalities = [];
    if (isset($SUB_DOMAIN)) {
      foreach ($SUB_DOMAIN as $domain) {
        if (!empty($domain['Locality'])) $SpecialLocalities[$domain['Locality']] = ['SubDomain' => $domain['SubDomain']];
      }
    }
    $this->config->set('SpecialLocalities', $SpecialLocalities);
  }

  private function setLocality($locality) {
    $loc_arr = explode(' ', $locality);
    $this->session->data['Locality'] = $locality;
    if (sizeof($loc_arr) > 1) {
      array_shift($loc_arr);
      $this->session->data['LocalityName'] = implode(' ', $loc_arr);
    } else {
      $this->session->data['LocalityName'] = $locality;
    }
  }

}

?>
