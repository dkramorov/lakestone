<?php

class ControllerCommonLocality extends Controller {
  
  public function index($ext = array()) {
    //now('loc_start');
    
    $this->load->model('extension/shipping/boxberry');
    $this->load->model('extension/shipping/cdek');
    $this->load->model('localisation/locality');
    
    if (isset($this->session->data['DPoint']))
      $data['DPoint'] = $this->session->data['DPoint'];
    else
      $data['DPoint'] = '';
    
    $data['tooltip'] = $this->session->data['tooltip'];
    $data['Locality'] = $this->session->data['Locality'];
    //now('loc0');
    if (in_array($this->session->data['Locality'], array(
        'г. Москва',
        'г. Санкт-Петербург',
    ))) {
      $data['PickPointMessage'] = 'Возможна бесплатная доставка курьером до двери <a href="/delivery">(условия доставки)</a>';
    } else {
      $data['PickPointMessage'] = '<a href="/delivery">Подробнее об условиях доставки</a>';
    }
    //now('loc1');
    $this->getLocalityCode('cdek');
    //now('loc2');
    if (!isset($this->session->data['places_count'])) {
      $loc_array = $this->getPlaces(true);
      $data['LocalityShort'] = $loc_array[0];
      $this->session->data['places_count'] = $data['places_count'] = sizeof($loc_array[1]['places'] ?? []);
    } else {
      $data['places_count'] = $this->session->data['places_count'];
    }
    
    //now('loc10');
    // Locality
    $data['LikesLocality'] = $this->cache->get('LikesLocality');
    if (!$data['LikesLocality']) {
      $data['LikesLocality'] = $this->model_localisation_locality->getLikesLocality(15);
      $this->cache->set('LikesLocality', $data['LikesLocality']);
    }
    $URI = parse_url($_SERVER['REQUEST_URI']);
    $Query = [];
    if (isset($URI['query'])) {
      parse_str($URI['query'], $Query);
    }
    $SL = $this->config->get('SpecialLocalities');
    foreach ($data['LikesLocality'] as &$locality) {
      $locality['SubDomain'] = '';
      $locality['SubURL'] = '';
      $locality['NewLocality'] = '';
      $locality['RemoteLocality'] = false;
      $locality['LocalLocality'] = false;
//            $Query['location'] = $locality['Locality'];
      if (isset($SL[$locality['Locality']]['SubDomain'])) {
        if ($SL[$locality['Locality']]['SubDomain'] !== $_SERVER['HTTP_HOST']) {
          // $locality['SubURL'] = 'https://' . $SL[$locality['Locality']]['SubDomain'] . $URI['path'] . '?' . http_build_query($Query);
          $locality['SubURL'] = 'https://' . $SL[$locality['Locality']]['SubDomain'] . ($URI['path'] ?? '');
          $locality['SubDomain'] = $SL[$locality['Locality']]['SubDomain'];
        }
      } elseif (!empty($SL['default'])) {
        if ($_SERVER['HTTP_HOST'] !== $SL['default']['SubDomain']) {
          $locality['SubURL'] = 'https://' . $SL['default']['SubDomain'] . $URI['path']; // . '?' . http_build_query($Query);
          $locality['RemoteLocality'] = urlencode($locality['Locality']);
          $locality['SubDomain'] = $SL['default']['SubDomain'];
        } else {
          $locality['LocalLocality'] = true;
        }
      } else {
        $locality['LocalLocality'] = true;
      }
    }
    if (!empty($ext)) {
      $ext[0]['placed_count'] = $data['places_count'];
      $ext[0]['pick_point'] = $this->getPickPoint($data['places_count']);
      if ($data['tooltip'])
        $ext[0]['LocalityShort'] = $data['LocalityShort'];
      $ext[0]['tooltip'] = $data['tooltip'];
    }
    //now('loc_end');
    return $this->load->view('common/locality_gmap', $data);
  }
  
  protected function NameCompare($name1, $name2) {
    $name1 = mb_strtolower($name1);
    $name2 = mb_strtolower($name2);
    if ($name1 === $name2)
      return true;
    $name1 = str_replace('ё', 'е', $name1);
    $name2 = str_replace('ё', 'е', $name2);
    if ($name1 === $name2)
      return true;
    return false;
  }
  
  protected function setDPointCity($provider, $city_code, $city_name) {
    if (!isset($this->session->data['DPoint']))
      return;
    if ($this->session->data['DPoint']['prov'] != $provider)
      return;
    if ($this->session->data['DPoint']['cid'] == $city_code)
      $this->session->data['DPoint']['CityName'] = 'г. ' . $city_name;
  }
  
  protected function setDPointAddress($provider, $id, $address) {
    if (!isset($this->session->data['DPoint']))
      return;
    if ($this->session->data['DPoint']['prov'] != $provider)
      return;
    if ($this->session->data['DPoint']['id'] == $id)
      $this->session->data['DPoint']['Address'] = $address;
  }
  
  public function search() {
    
    $json = array(
        'result' => array()
    );
    
    $this->load->model('localisation/locality');
    $SL = $this->config->get('SpecialLocalities');
//        foreach ($SL as $dom) {
//            if ($dom['Locality'] == 'default') {
//                $DefaultDomain = $dom['SubDomain'];
//                break;
//            }
//        }
    
    if (isset($this->request->post['name']) and !empty($this->request->post['name'])
    ) {
      $result = $this->model_localisation_locality->searchLocality(array(
          'name' => '%' . $this->request->post['name'] . '%',
      ));
      foreach ($result as $loc) {
        if (!empty($SL[$loc])) {
          if (!empty($SL[$loc]['SubDomain'])) {
            $json['result'][$loc] = ['SubDomain' => $SL[$loc]['SubDomain']];
          }
        } else {
          $json['result'][$loc] = [
              'RemoteDomain' => $this->config->get('SpecialLocalities')['default']['SubDomain'],
          ];
        }
      }
//            $json['result'] = $result;
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }
  
  public function setRemoteLocality() {
    $json = [];
    if (!empty($this->request->get['loc'])) {
      $loc_arr = explode(' ', $this->request->get['loc']);
      $this->session->data['Locality'] = $this->request->get['loc'];
      if (sizeof($loc_arr) > 1) {
        array_shift($loc_arr);
        $this->session->data['LocalityName'] = implode(' ', $loc_arr);
      } else {
        $this->session->data['LocalityName'] = $this->request->get['loc'];
      }
      $json['status'] = 'OK';
      unset($this->session->data['LocalityCodes']);
      $loc_array = $this->getPlaces(true);
      if (empty($loc_array[1]))
        $this->session->data['places_count'] = 0;
      else
        $this->session->data['places_count'] = sizeof($loc_array[1]['places']);
      unset($this->session->data['DPoint']);
    }
    if (!empty($_SERVER['HTTP_ORIGIN'])) {
      $this->response->addHeader('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      $this->response->addHeader('Access-Control-Allow-Credentials: true');
      $this->response->addHeader('Content-Type: application/json');
    }
    $this->response->setOutput(json_encode($json));
  }
  
  public function setLocality() {
    
    $json = array(
        'result' => array()
    );
    
    if (isset($this->request->get['name']) and !empty($this->request->get['name'])
    ) {
      
      $this->load->model('localisation/locality');
      foreach ($this->model_localisation_locality->getNameTypes() as $type) {
        if (mb_substr($this->request->get['name'], 0, mb_strlen($type)) == $type) {
          $this->session->data['LocalityName'] = trim(str_replace($type, '', $this->request->get['name']));
          break;
        }
      }
      
      $this->session->data['Locality'] = $this->request->get['name'];
      unset($this->session->data['LocalityCodes']);
      $loc_array = $this->getPlaces(true);
      if (empty($loc_array[1]))
        $this->session->data['places_count'] = 0;
      else
        $this->session->data['places_count'] = sizeof($loc_array[1]['places']);
      unset($this->session->data['DPoint']);
      $json['result'] = 'ok';
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }
  
  public function setPlace() {
    
    $json = array(
        'result' => array()
    );
    
    if (isset($this->request->get['dpoint']) and !empty($this->request->get['dpoint'])
    ) {
      $this->session->data['DPoint'] = $this->request->get['dpoint'];
      $this->session->data['DPointInfo']['CityName'] = $this->session->data['Locality'];
      if (isset($this->request->get['addr']) and !empty($this->request->get['addr']))
        $this->session->data['DPointInfo']['Address'] = $this->request->get['addr'];
      $json['result'] = 'ok';
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }
  
  public function getSetupDelivery() {
    
    $data = array(
        'Locality' => $this->session->data['Locality'],
        'pick_point' => $this->getPickPoint($this->session->data['places_count'])
    );
    $this->response->setOutput($this->load->view('common/setup_delivery', $data));
  }
  
  public function getEmptyPickPoint() {
    $data = array(
        'telephone' => $this->config->get('config_telephone'),
        'telephone_href' => str_replace(array(' ', '(', ')', '-'), '', $this->config->get('config_telephone')),
    );
    $this->response->setOutput($this->load->view('common/empty_pp', $data));
  }
  
  public function getSetupDeliveryM() {
    $data = array(
        'Locality' => $this->session->data['Locality'],
    );
    $this->response->setOutput($this->load->view('common/setup_delivery_m', $data));
  }
  
  public function getPlaces($short = false) {
    
    $json = array();
    $result = array();
    $City = false;
    
    if (isset($this->request->get['locality']) and !empty($this->request->get['locality'])
    ) {
      $City = $this->request->get['locality'];
    }
    if ($short)
      $City = $this->session->data['Locality'];
    if ($City) {
      $this->load->model('localisation/locality');
      foreach ($this->model_localisation_locality->getNameTypes() as $type) {
        if (mb_substr($City, 0, mb_strlen($type)) == $type) {
          $City = trim(str_replace($type, '', $City));
          break;
        }
      }
      //now('place 1');
      $result = $this->getBoxBerryPlaces($City, $result);
      //now('place 2');
      $result = $this->getCdekPlaces($City, $result);
      //now('place 3');
      if ($short)
        return array($City, $result);
      if (!empty($result['places'])) {
        foreach ($result['places'] as $key => $row) {
          $address[$key] = $row['address'];
        }
        array_multisort($address, SORT_ASC, SORT_STRING, $result['places']);
      }
      $json = array_merge($json, $result);
      $json['status'] = 'OK';
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json, JSON_NUMERIC_CHECK));
  }
  
  private function fixAddress($address) {
    // return $address;
#		$res = preg_replace('/^(?|(ул|ш|пр-т|пр|б-р|пер)\.?)\s*(\S[^,]+)(.+)$/', '\2 \1\3', $address);
    $res = preg_replace('/^(ул|ш|пр-т|пр|б-р|пер|пр-т|наб|пл)\.?\s*(\S[^,]+)(.+)$/', '\2 \1\3', $address);
    #$res = preg_replace('/^(ш\.?)\s*(\S[^,]+)(.+)$/', '\2 \1\3', $res);
    #$res = preg_replace('/^(пр-т\.?)\s?(\S[^,]+)(.+)$/', '\2 \1\3', $res);
    #$res = preg_replace('/^(пр\.?)\s?(\S[^,]+)(.+)$/', '\2 \1\3', $res);
    #$res = preg_replace('/^(б-р\.?)\s?(\S[^,]+)(.+)$/', '\2 \1\3', $res);
    #$res = preg_replace('/^(пер\.?)\s?(\S[^,]+)(.+)$/', '\2 \1\3', $res);
    return $res;
  }
  
  private function setLocalityCode($provider, $code) {
    if (!isset($this->session->data['LocalityCodes']))
      $this->session->data['LocalityCodes'] = array();
    $this->session->data['LocalityCodes'][$provider] = $code;
  }
  
  private function getLocalityCode($provider) {
    if (isset($this->session->data['LocalityCodes'][$provider]))
      return $this->session->data['LocalityCodes'][$provider];
    $this->getPlaces(true);
    if (isset($this->session->data['LocalityCodes'][$provider]))
      return $this->session->data['LocalityCodes'][$provider];
    else
      return false;
  }
  
  private function getBoxBerryPlaces($locality, $result = array()) {
    
    $this->load->model('extension/shipping/boxberry');
    
    foreach ($this->model_extension_shipping_boxberry->ListCities() as $boxberry) {
      if ($this->NameCompare($boxberry['Name'], $locality)) {
        $this->setLocalityCode('boxberry', $boxberry['Code']);
        $bb_points = $this->model_extension_shipping_boxberry->ListPoints(array(
            'city_code' => $boxberry['Code'],
        ));
        if ($this->config->get('debug'))
          $this->log->write('BoxBerry (' . $locality . ' [' . $boxberry['Code'] . ']): ' . sizeof($bb_points));
        foreach ($bb_points as $bb_point) {
          if (empty($bb_point['GPS']))
            continue;
          $bb_coords = explode(',', $bb_point['GPS']);
          if (empty($result['bounds']))
            $result['bounds'][0] = $result['bounds'][1] = $bb_coords;
          if ($result['bounds'][0][0] > $bb_coords[0])
            $result['bounds'][0][0] = $bb_coords[0];
          if ($result['bounds'][0][1] > $bb_coords[1])
            $result['bounds'][0][1] = $bb_coords[1];
          if ($result['bounds'][1][0] < $bb_coords[0])
            $result['bounds'][1][0] = $bb_coords[0];
          if ($result['bounds'][1][1] < $bb_coords[1])
            $result['bounds'][1][1] = $bb_coords[1];
          $result['places'][] = array(
              'Provider' => 'boxberry',
              'ID' => $bb_point['Code'],
              'CityCode' => $boxberry['Code'],
              'GPS' => explode(',', $bb_point['GPS']),
              'address' => $bb_point['AddressReduce'],
            /* 'guide'		=> 'Проезд: ' . str_replace(
              array("\n", "'"),
              array('<br/>', '"'),
              wordwrap($bb_point['TripDescription'],80,"\n")
              ) . '<br/>' . $bb_point['Phone'] . '<br/>'
              . $bb_point['WorkSchedule'] . '<br/>', */
              'guide' => 'Проезд: ' . str_replace(
                      array("\n", "'"),
                      array(' ', '"'),
                      wordwrap($bb_point['TripDescription'], 80, "\n")
                  ) . '<br/>Тел. ' . $bb_point['Phone'] . '<br/>'
                  . ($bb_point['WorkSchedule'] ?? ''),
          );
        }
      }
    }
    return $result;
  }
  
  private function getCdekPlaces($locality, $result = array()) {
    
    $this->load->model('extension/shipping/cdek');
    
    foreach ($this->model_extension_shipping_cdek->ListCities() as $cdek) {
      if ($this->NameCompare($cdek['Name'], $locality)) {
        $this->setLocalityCode('cdek', $cdek['Code']);
        $cdek_points = $this->model_extension_shipping_cdek->ListPoints(array(
            'city_code' => $cdek['Code'],
        ));
        if ($this->config->get('debug'))
          $this->log->write('CDEK(' . $locality . ' [' . $cdek['Code'] . ']): ' . sizeof($cdek_points));
        foreach ($cdek_points as $cdek_point) {
          if (empty($cdek_point['GPS']))
            continue;
          if (empty($result['bounds']))
            $result['bounds'][0] = $result['bounds'][1] = $cdek_point['GPS'];
          if ($result['bounds'][0][0] > $cdek_point['GPS'][0])
            $result['bounds'][0][0] = $cdek_point['GPS'][0];
          if ($result['bounds'][0][1] > $cdek_point['GPS'][1])
            $result['bounds'][0][1] = $cdek_point['GPS'][1];
          if ($result['bounds'][1][0] < $cdek_point['GPS'][0])
            $result['bounds'][1][0] = $cdek_point['GPS'][0];
          if ($result['bounds'][1][1] < $cdek_point['GPS'][1])
            $result['bounds'][1][1] = $cdek_point['GPS'][1];
          $result['places'][] = array(
              'Provider' => 'cdek',
              'ID' => $cdek_point['ID'],
              'CityCode' => $cdek['Code'],
              'GPS' => $cdek_point['GPS'],
              'address' => $this->fixAddress($cdek_point['AddressReduce']),
              'guide' => $cdek_point['Note'] . '</br>'
                  . $cdek_point['Phone'] . '</br>'
                  . $cdek_point['WorkSchedule'],
          );
        }
      }
    }
    return $result;
  }
  
  private function getPickPoint($count) {
    if ($count == 0) {
      $result = '<a title="Посмотреть условия доставки" class="blue" href="/delivery">Доставка по России бесплатно</a>';
    } else {
      switch (substr((string)$count, -1)) {
        case 1:
          $pt = 'пункт';
          break;
        case 2:
        case 3:
        case 4:
          $pt = 'пункта';
          break;
        default:
          $pt = 'пунктов';
      }
      $result = '<a title="Посмотреть и выбрать Пункт Выдачи Заказа" class="blue" role="button" data-toggle="modal" data-target="#order_placing">' . $count . ' ' . $pt . ' выдачи заказов</a>';
    }
    return $result;
  }
  
}
