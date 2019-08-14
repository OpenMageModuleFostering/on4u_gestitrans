<?php

class On4u_Gestitrans_Model_On4u_Gestitrans extends Mage_Shipping_Model_Carrier_Abstract {

    protected $_code = 'Gestitrans';


    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $action = Mage::app()->getFrontController()->getAction();
        $nombre = $action->getFullActionName();
        //Mage::log("Controller:" . $nombre);


        $err = null;
        $envio = array();

        $username = $this->getConfigData('username');
        $password = $this->getConfigData('password');

        $userData = Mage::getModel('Gestitrans/On4u_Source_User')->getUserData($username, $password);
        $cpFrom = ($userData['postalCode']) ? $userData['postalCode'] : 28001;
        $countryFrom = ($userData['country']) ? $userData['country'] : 'ES';

        $cpTo = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getPostcode();
        $countryTo = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCountry();

        $countryList = $this->getConfigData('specificcontries');
        $countries = explode(",", $countryList);

        //if(in_array($countryTo,$countries)){

        $maxHeight = $this->getConfigData('max_weight');
        $orden = $this->getConfigData('sort');

        $tipoMargen = $this->getConfigData('tipo_margen');
        $valorMargen = $this->getConfigData('valor_margen');

        $dimensions = explode('x', $this->getConfigData('box_dimensions'));
        $length = $dimensions[0];
        $width = $dimensions[1];
        $height = $dimensions[2];

        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $weightPerBulk = 0;
        $weight = 0;
        $totalItems = 0;
        $bulkList = "[";
        $itemCounter = 0;
        $totalItemCounter = 0;
        $itemsPerBulk = 1;
        $price = 0;

        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());
            $totalItems += $item->getQty();
        }
        foreach ($items as $product) {
            for ($count = 0; $count < $product->getQty(); $count++) {
                $weightPerBulk += $product->getWeight();
                $itemCounter++;
                $totalItemCounter++;

                if ($weightPerBulk >= $maxHeight) {
                    if ($bulkList != "[")
                        $bulkList .= ",";
                    $newWeight = $weightPerBulk - $product->getWeight();
                    $bulkList .= '["' . $length . '","' . $width . '","' . $height . '","' . $newWeight . '","1"]';
                    $weightPerBulk = $product->getWeight();
                }
                //if ($itemCounter == $itemsPerBulk || $totalItemCounter == $totalItems) {
                if ($totalItemCounter == $totalItems) {
                    if ($bulkList != "[")
                        $bulkList .= ",";
                    $bulkList .= '["' . $length . '","' . $width . '","' . $height . '","' . $weightPerBulk . '","1"]';
                    $itemCounter = 0;
                    $weightPerBulk = 0.0;
                }
            }
        }
        $bulkList .= "]";

        $metodos = Mage::getModel('Gestitrans/On4u_Source_Method')->toOptionArray($countries, $username, $password, $countryFrom, $cpFrom, $countryTo, $cpTo, $bulkList, $orden);


        $result = Mage::getModel('shipping/rate_result');

        if ($err) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($err);
            $result->append($error);
        } else {
            foreach ($metodos as $metodo) {
                if ($tipoMargen == "porcentaje") {
                    $price = $metodo["price"] * ($valorMargen / 100) + $metodo["price"];
                } else
                    $price = $metodo["price"] + $valorMargen;

                $aP = explode(',', $this->getConfigData('specificproviders'));

                if (in_array($metodo["value"], $aP)) {
                    $envio["metodo"] = $metodo;

                    $envio["importe"] = 0;
                    $envio["importeFinal"] = 0;
                    $rate = Mage::getModel('shipping/rate_result_method');

                    $rate->setCarrier($this->_code);
                    $rate->setCarrierTitle($this->getConfigData('title'));
                    $rate->setMethod($metodo["value"]);
                    $rate->setMethodTitle($metodo["label"] . " - " . $metodo["duration"] . " d. -");
                    $rate->setMethodDescription($metodo["duration"]);

                    $rate->setCost($price);
                    $rate->setPrice($price);
                    $result->append($rate);
                }
            }
        }

        //}
        return $result;
    }

    function calcularPeso() {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $weight = 0;
        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());
        }
        return $weight;
    }

    function compararPrecio($x, $y) {

        if ($x['price'] == $y['price'])
            return 0;
        else if ($x['price'] > $y['price'])
            return -1;
        else
            return 1;
    }

    function compararDuracion($x, $y) {

        if ($x['duration'] == $y['duration'])
            return 0;
        else if ($x['duration'] > $y['duration'])
            return -1;
        else
            return 1;
    }

    function compararNombre($x, $y) {

        if ($x['label'] == $y['label'])
            return 0;
        else if ($x['label'] > $y['label'])
            return -1;
        else
            return 1;
    }

    public function isTrackingAvailable() {
        return false;
    }

    public function getAllowedMethods() {
        return array('gestitrans' => $this->getConfigData('name'));
    }

}

