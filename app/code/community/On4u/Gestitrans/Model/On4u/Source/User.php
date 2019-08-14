<?php

class On4u_Gestitrans_Model_On4u_Source_User {

    public function getUserData($username, $password) {

        $urlDesPaises = "http://gestitrans.ibermatica.com/service/rest/integration/address/";  /* @agoya -> No funciona desarrollo */
        $urlProPaises = "https://www.gestitrans.com/service/rest/integration/address/";

        $datos = json_decode(file_get_contents($urlProPaises.'?userName='.$username.'&password='.$password), true);

        return $datos;
    }
}

?>
