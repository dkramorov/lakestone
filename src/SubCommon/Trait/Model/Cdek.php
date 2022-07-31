<?php

namespace Lakestone\SubCommon\Trait\Model;

use Cache;
use DOMDocument;
use DOMXPath;
use Exception;
use Throwable;

trait Cdek {
  
  private $CURL;
  private $Cache;
  private $ShortCache;
  
  public function request($data) {
    $ch = $this->getCURL();
    // $date = strftime('%Y-%M-%dT%H:%M:%S');
    $date = strftime('%Y-%m-%d');
    $secure = md5($date . '&' . CDEK_PASSWORD);
    if (isset($data['api'])) {
      $url = CDEK_API;
      $params = array_merge(
          array(
              'version' => '1.0',
              'dateExecute' => $date,
              'authLogin' => CDEK_LOGIN,
              'secure' => $secure,
          ),
          $data['fields']
      );
    } else {
      $url = CDEK_BASE;
      $params = array_merge(
          $data['fields'],
          array(
              'Account' => CDEK_LOGIN,
              'Secure' => $secure,
              'Data' => $date,
          )
      );
    }
    $query = http_build_query($params);
    $url .= $data['endpoint'];
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if ($data['method'] == 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
      if (isset($data['api'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
      } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
      }
    } elseif (!empty($query)) {
      curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
    }
    
    $res_str = curl_exec($ch);
    $header = substr($res_str, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $body = substr($res_str, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $ah = explode("\n", $header);
    if (stripos($ah[0], '200') == FALSE) {
      throw new Exception("CURL has returned: $res_str\nCURL getinfo: " . json_encode(curl_getinfo($ch)));
    }
    return $body;
  }
  
  public function calcDelivery($city_code, $tariff = 137) {
    $cache_name = 'cdek_delivery_' . $tariff . '_' . $city_code;
    $ret = $this->cache->get($cache_name);
    if ($ret) {
      return $ret;
    }
    try {
      $req = array(
          'fields' => array(
              'senderCityId' => 44,
              'receiverCityId' => $city_code,
              'tariffId' => $tariff,
            // 'tariffList'      => array(),
            // 'modeId'          => 4,
              'goods' => array(array(
                  'weight' => 1,
                  'length' => 30,
                  'width' => 30,
                  'height' => 5,
              )),
            // 'services'        => array(
            //   array('id' => 2, 'param' => 2000),
            // ),
          ),
          'api' => true,
          'method' => 'POST',
          'endpoint' => 'calculator/calculate_price_by_json.php',
      );
      $ret = json_decode($this->request($req));
      if (isset($ret->result)) {
        $this->cache->set($cache_name, $ret->result);
        return $this->cache->get($cache_name);
      } else {
        $this->log->write('The error from CDEK:' . $ret->error[0]->text);
        $this->log->write('CDEK Error dump:' . json_encode($ret));
        return array();
      }
    } catch (Exception $e) {
      $this->log->write('An error from CDEK: ' . $e->getMessage());
      return array();
    }
  }
  
  public function ListCities() {
    $ret = $this->getCache()->get('cdek_pvzlist.c');
    if ($ret) {
      return $ret;
    }
    $ret = [];
    try {
      $X = $this->getList();
      $XP = new DOMXPath($X);
      $NL = $XP->query('//Pvz[@CountryCode="1"]');
      $cities = [];
      foreach ($NL as $P) {
        $A = $P->attributes;
        if (!in_array($A->getNamedItem('CityCode')->nodeValue, $cities)) {
          $ret[] = array(
              'Name' => $A->getNamedItem('City')->nodeValue,
              'Code' => $A->getNamedItem('CityCode')->nodeValue,
          );
          $cities[] = $A->getNamedItem('CityCode')->nodeValue;
        }
      }
      $this->getCache()->set('cdek_pvzlist.c', $ret);
    } catch (\Throwable $e) {
      $this->log->write('CDEK CityList building error:' . $e->getMessage());
    }
    if (empty($ret)) {
      $this->log->write('CDEK CityList is empty');
    }
    return $ret;
  }
  
  public function findCityByPP(string $pp_code): string {
    $ret = '';
    try {
      $X = $this->getList();
      $XP = new DOMXPath($X);
      $NL = $XP->query('//Pvz[@Code="' . $pp_code . '"]');
      if ($NL->length == 0) {
        throw new Exception('pickpoint ' . $pp_code . ' not found');
      }
      $ret = $NL->item(0)->attributes->getNamedItem('CityCode')?->nodeValue ?? '';
    } catch (\Throwable $e) {
      $this->log->write('CDEK finding city by pp_code has an error: ' . $e->getMessage());
    }
    if (empty($ret)) {
      $this->log->write('CDEK city not found by pp_code');
    }
    return $ret;
  }
  
  public function ListPoints($data) {
    $ret = $this->getCache()->get('cdek_pvzlist.p.' . $data['city_code']);
    if ($ret) {
      return $ret;
    }
    $ret = [];
    try {
      $X = $this->getList();
      $XP = new DOMXPath($X);
      $NL = $XP->query('//Pvz[@CityCode="' . $data['city_code'] . '"]');
      foreach ($NL as $P) {
        $A = $P->attributes;
        $PA = array(
            'ID' => $A->getNamedItem('Code')->nodeValue,
            'AddressReduce' => $A->getNamedItem('Address')->nodeValue,
            'GPS' => array($A->getNamedItem('coordY')->nodeValue, $A->getNamedItem('coordX')->nodeValue),
            'Note' => $A->getNamedItem('Note')->nodeValue,
            'Phone' => $A->getNamedItem('Phone')->nodeValue,
            'Type' => $A->getNamedItem('Type')->nodeValue
//            'WorkSchedule'	=> $A->getNamedItem('WorkTime')->nodeValue,
        );
        if ($A->getNamedItem('WorkTime')) {
          $PA['WorkSchedule'] = $A->getNamedItem('WorkTime')->nodeValue;
        } else {
          $PA['WorkSchedule'] = '';
        }
        
        # исключаем постамыты из ПВЗ на сайте
        if ($PA['Type'] == 'POSTAMAT') {
          continue;
        }
        
        $ret[] = $PA;
      }
      $this->getCache()->set('cdek_pvzlist.p.' . $data['city_code'], $ret);
    } catch (Throwable $e) {
      $this->log->write('CDEK PointsList (' . $data['city_code'] . ') building error:' . $e->getMessage());
    }
    if (empty($ret)) {
      $this->log->write('CDEK PointsList for "' . $data['city_code'] . '" is empty');
    }
    return $ret;
  }
  
  private function getList(): DOMDocument {
    $X = new DOMDocument;
    try {
      $ret = $this->getCache()->get('cdek_pvzlist');
      if (
          !is_string($ret)
          or strlen($ret) < 20
      ) {
        $ret = $this->loadFullList();
        $this->getCache()->set('cdek_pvzlist', $ret);
          $this->getCache()->set('cdek_pvzlist', $ret);
      }
      $X->loadXML($ret);
    } catch (Throwable $e) {
      $this->log->write('CDEK List building error:' . $e->getMessage());
      $this->log->write('CDEK turned OFF!!!');
      $this->getShortCache()->set('cdek_pvzlist', true);
    }
    return $X;
  }
  
  public function forceGetList() {
    $this->getCache()->set('cdek_pvzlist', $this->loadFullList());
  }
  
  private function loadFullList() {
    $ret = file_get_contents(DIR_DOWNLOAD . 'cdek_pvzlist.xml');
    if (!empty($ret)) {
      return $ret;
    }
    $req = array(
        'fields' => array(),
        'method' => 'GET',
        'endpoint' => 'pvzlist.php',
    );
    $ret = $this->request($req);
    if (strlen($ret) < 20) {
      throw new Exception('Response is too small: ' . $ret);
    }
    return $ret;
  }
  
  private function getCache(): Cache {
    if ($this->Cache === null) {
      $this->Cache = new Cache('file', 3600 * 24 * 3);
    }
    return $this->Cache;
  }
  
  private function getShortCache(): Cache {
    if ($this->ShortCache === null) {
      $this->ShortCache = new Cache('file', 60 * 10);
    }
    return $this->ShortCache;
  }
  
  protected function getCURL() {
    if ($this->CURL === NULL) {
      $this->CURL = curl_init();
      curl_setopt($this->CURL, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($this->CURL, CURLOPT_CONNECTTIMEOUT, 1);
      curl_setopt($this->CURL, CURLOPT_CONNECTTIMEOUT_MS, 700);
    }
    return $this->CURL;
  }
  
}