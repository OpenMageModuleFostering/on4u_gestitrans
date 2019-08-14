<?php

class On4u_Gestitrans_Model_On4u_Source_Method {

    public function toOptionArray($countries, $username, $password, $countryFrom, $cpFrom, $countryTo, $cpTo, $weight, $orden) {
        $start_time = microtime(true);
        $arr = array();

        $urlProTransportistas = "https://www.gestitrans.com/service/rest/integration/search";
        $urlDesTransportistas = "http://gestitrans.ibermatica.com/service/rest/integration/search";


        $url = $urlProTransportistas.'/?userName=' . $username .
            '&password=' . $password . '&pickUpDate=&countryFromFormat=iso2&countryFrom=' . $countryFrom . '&cpFrom=' . $cpFrom . '&countryToFormat=iso2&countryTo=' . $countryTo . '&cpTo=' . $cpTo .
            '&shopType=magento&shopName=&bulkList='.$weight;


        /* @agoya-> Debug*/
        /*Mage::log('$url:'.$url, Zend_Log::INFO);
        Mage::log('$username:'.$username, Zend_Log::INFO);
        Mage::log('$password:'.$password, Zend_Log::INFO);
        Mage::log('$cpFrom:'.$cpFrom, Zend_Log::INFO);
        Mage::log('$cpTo:'.$cpTo, Zend_Log::INFO);
        Mage::log('$countryFrom:'.$countryFrom, Zend_Log::INFO);
        Mage::log('$countryTo:'.$countryTo, Zend_Log::INFO);
        Mage::log('$weight:'.$weight, Zend_Log::INFO);
        Mage::log('----------------------------:', Zend_Log::INFO);*/


        if(in_array($countryTo,$countries) || $countries==$countryTo){

            //Mage::log("Empieza a obtener los datos: ".round((microtime(true) - $start_time),4)." segundos.");
            $datos = json_decode(file_get_contents($url), true);
            //Mage::log("Ha obtenido los datos: ".round((microtime(true) - $start_time),4)." segundos.");
            //Mage::log("Count datos[results]".count($datos['results']));
            $size = count($datos['results']);
            for ($i = 0; $i < $size ; $i++) {
                $arr[] = array(
                    'value' => $datos['results'][$i]['providerUid'],
                    'label' => $datos['results'][$i]['providerName'],
                    'price' => $datos['results'][$i]['priceWithVAT'],
                    'duration' => $datos['results'][$i]['duration']
                );
            }
            usort($arr, array($this, $orden));
        }
        return $arr;
    }

    function tiempo($a, $b) {
        if ($a['duration'] == $b['duration']) {
            return 0;
        }
        return ($a['duration'] < $b['duration']) ? -1 : 1;
    }

    function precio($a, $b) {
        if ($a['price'] == $b['price']) {
            return 0;
        }
        return ($a['price'] < $b['price']) ? -1 : 1;
    }

    public function getMethod($shippingMethod) {
        $mte = explode("_", $shippingMethod);
        if ($mte[0] == "Gestitrans") {
            $label = $mte[1];
            foreach ($this->toOptionArray() as $mta) {
                if ($mta["label"] == $label)
                    $metodo = $mta;
            }
        }
        return $metodo;
    }
}
?>
