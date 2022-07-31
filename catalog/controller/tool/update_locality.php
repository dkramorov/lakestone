<?php
class ControllerToolUpdateLocality extends Controller {

    private $localitytypes = 'http://www.oktmo.ru/list_localitytypes/';
    private $localitylist = 'http://www.oktmo.ru/locality_registry/';
    private $geoip = 'http://ipgeobase.ru/files/db/Main/geo_files.zip';

    private $Types;

    public function index() {

        set_time_limit(0);
        ignore_user_abort(TRUE);

        $this->load->model('localisation/locality');
        
        echo "Starting...";
        flush();
        $this->updateGeoIP($this->geoip);
        echo "<p>import GeoIP is done</p>\n";
        flush();
        #$this->updateTypes($this->localitytypes);
        #echo "<p>import localitytypes is done</p>\n";
        flush();
        #$this->updateLocalities($this->localitylist);
        #echo "\n<p>whole import is complete</p>\n";
    }

    protected function updateGeoIP($source) {
        $zip = tempnam(sys_get_temp_dir(), 'geoip');
        file_put_contents($zip, file_get_contents($source));
        $ZIP = new ZipArchive;
        if ($ZIP->open($zip)) {
            $cities = $ZIP->getStream('cities.txt');
            while (!feof($cities)) {
                $city = explode("\t", iconv('CP1251', 'UTF-8', fgets($cities)));
                $this->model_localisation_locality->updateGeoIPCity(array(
                    'city_id'	=> $city[0],
                    'name'	=> $city[1],
                    'area'	=> $city[2],
                    'region'	=> $city[3],
                    'Lat'	=> $city[4],
                    'Lng'	=> $city[5],
                ));
            }
            fclose($cities);
            $cidrs = $ZIP->getStream('cidr_optim.txt');
            if ($cidrs !== False and !feof($cidrs))
                $this->model_localisation_locality->resetGeoIPCidr();
            while (!feof($cidrs)) {
                $cidr = explode("\t", fgets($cidrs));
                if ($cidr[4] == '-')
                    $cidr[4] = 0;
                $this->model_localisation_locality->updateGeoIPCidr(array(
                    'from'	=> $cidr[0],
                    'end'	=> $cidr[1],
                    'cc'	=> $cidr[3],
                    'city_id'	=> $cidr[4],
                ));
            }
            fclose($cidrs);
            if ($this->model_localisation_locality->countGeoIPCidr() > 0)
                $this->model_localisation_locality->clearGeoIPCidr();
        }
        $ZIP->close();
        unlink($zip);
    }
    
    protected function updateLocalities($source) {

        $List = new DOMDocument();
        @$List->loadHTML(file_get_contents($source));
        $XP = new DOMXPath($List);
        
        try {

            $Table = $XP->query("//div[@id='container']/p/*/a");
            if ($Table->length == 0)
                throw new Exception('Not found container of locality in source');
            $this->Types = $this->model_localisation_locality->getNameTypes();

            # fix для Воронежской области
            $prefix = $this->updateLocalitiesFromURL('http://www.oktmo.ru/locality_registry/?code=20000000000');
            echo "<p>import from $prefix is done<br/>\n"; flush();
            ##############
            
            foreach ($Table as $N) {
                $URL = $N->attributes->getNamedItem('href')->nodeValue;
                
                $prefix = $this->updateLocalitiesFromURL($URL);
                
                echo "<p>import from $prefix is done<br/>\n";
                flush();
            }

        } catch( Exception $e) {
            var_dump($e);
        }

    }

    protected function updateLocalitiesFromURL($URL) {
        parse_str(parse_url($URL, PHP_URL_QUERY), $res);
        $prefix = substr($res['code'], 0, 2);
        $X = new DOMDocument();
        @$X->loadHTML(file_get_contents($URL));
        $P = new DOMXPath($X);
        $XL = $P->query("//div[@id='container']/p");
        foreach ($XL as $XN) {
            $B = $XN->getElementsByTagName('b');
            $type_name = $text = '';
            $type_id = 0;
            if ($B->length == 0)
                continue;
            if (substr($B->item(0)->nodeValue, 0, 2) != $prefix)
                continue;
            foreach ( $XN->childNodes as $nc) {
                if ( $nc->nodeType == XML_TEXT_NODE ) {
                    $text = trim($nc->nodeValue);
                    break;
                }
            }
            if (empty($text))
                continue;
            foreach ($this->Types as $id => $type) {
                if (mb_substr($text, 0, mb_strlen($type)) == $type) {
                    $type_id = $id;
                    $type_name = $type;
                    $text = trim(str_replace($type, '', $text));
                    break;
                }
            }
            $this->model_localisation_locality->updateLocality(array(
                'name'			=> $text,
                'locality_type_id'	=> $type_id,
            ));
            echo 'Updated ' . $type_name . ' ' . $text . "<br/>\n"; flush();
        }
        return $prefix;
    }
    
    protected function updateTypes($source) {
    
        $List = new DOMDocument();
        @$List->loadHTML(file_get_contents($source));
        $XP = new DOMXPath($List);
    
        try {
        
            $Table = $XP->query("//div[@id='container']/table");
            if ($Table->length == 0)
                throw new Exception('Not found table of localitytypes in source');
            $NL = $XP->query('tr', $Table->item(0));
            foreach ($NL as $N) {
                $td = $N->getElementsByTagName('td');
                if ( $td->length == 2 ) {
                    $this->model_localisation_locality->updateType(array(
                        'name'		=> $td->item(0)->nodeValue,
                        'description'	=> $td->item(1)->nodeValue,
                    ));
                }
            }
            
        } catch( Exception $e) {
        
            var_dump($e);
            
        }

    }

}
