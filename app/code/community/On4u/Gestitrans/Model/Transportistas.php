<?php

class On4U_Gestitrans_Model_Transportistas {

    public function toOptionArray() {

        $urlDesTransportistas = "http://gestitrans.ibermatica.com/service/rest/integration/search";
        $urlProTransportistas = "https://www.gestitrans.com/service/rest/integration/search";

        $arr = array();
        $gestitrans = new On4u_Gestitrans_Model_On4u_Gestitrans();
        $username = $gestitrans->getConfigData('username');
        $password = $gestitrans->getConfigData('password');


        $userData = Mage::getModel('Gestitrans/On4u_Source_User')->getUserData($username, $password);
        $cpFrom = ($userData['postalCode']) ?$userData['postalCode'] : 28001;
        $countryFrom =($userData['country']) ?$userData['country'] : 'ES';

        $orden = $gestitrans->getConfigData('sort');

        /* @agoya
         * Es necesario pasarle un codigo postal y un pais para obtener los transportistas.
         * Estaría bien que hubiese una llamada que te devolviese TODOS los transportistas, para filtrarlos a continuación en la configuración del modulo.
         * Una opcion es hacer un for por cada pais y sumar todos los proveedores. Haria falta un codigo postal oficial por cada país. //$countries = $gestitrans->getConfigData('specificcontries');
         */
        $data=json_decode(file_get_contents($urlProTransportistas.'/?userName=' . $username . '&password=' . $password .
                  '&pickUpDate=&countryFromFormat=iso2&countryFrom='.$countryFrom.'&cpFrom='.$cpFrom.'&countryToFormat=iso2&countryTo=ES&cpTo=28001&shopType=magento&shopName='.
                  '&bulkList=[["1","2","2","2","1"]]'),true);

        for ($i=0;$i<count($data['results']);$i++){
            $arr[] = array(
                'value'=>$data['results'][$i]['providerUid'],
                'label'=>strtoupper($data['results'][$i]['providerName']." - ".$data['results'][$i]['price']." €"),
                'price' => $data['results'][$i]['price'],
                'duration' => $data['results'][$i]['duration']
                );
        }
        usort($arr, array($this, $orden));

        return $arr;
    }
    
    function tiempo($a, $b){
        
        if ($a['duration'] == $b['duration']) {
            return 0;
        }
        return ($a['duration'] < $b['duration']) ? -1 : 1;
    }

    function precio($a, $b){
        if ($a['price'] == $b['price']) {
            return 0;
        }
        return ($a['price'] < $b['price']) ? -1 : 1;
    }

}

