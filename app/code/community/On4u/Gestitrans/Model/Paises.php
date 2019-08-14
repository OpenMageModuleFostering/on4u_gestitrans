<?php

class On4U_Gestitrans_Model_Paises {

    public function toOptionArray() {
        
        $urlDesPaises = "http://gestitrans.ibermatica.com/service/rest/integration/countryList"; /* @agoya -> La url de desarrollo no funciona */
        $urlProPaises = "https://www.gestitrans.com/service/rest/integration/countryList";
        $arr = array();
       
	$data=json_decode(file_get_contents($urlProPaises),true);
       
	for ($i=0;$i<count($data);$i++){
        
                $arr[] = array(
                    'value'=> $data[$i]['countryISO'],
                    'label'=> strtoupper($data[$i]['countryName']),
                    'id' => $data[$i]['id'],
                    'minZip' => $data[$i]['minZip'],
                    'maxZip' => $data[$i]['maxZip'],
                    'dialCode' => $data[$i]['dialCode'],
                    'hasZip' => $data[$i]['hasZip'],
                    'zipObligatorio' => $data[$i]['zipObligatorio'],
                    'zipNoNumerico' => $data[$i]['zipNoNumerico'],
                    'ce' => $data[$i]['ce']
                    );
        }

        asort($arr); /* @agoya-> Lo ordena por el codigo internacional (ISO), que es el value. No el label */
        return $arr;
    }


    
}

