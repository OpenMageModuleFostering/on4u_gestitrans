<?php


// Mage_Core_Controller_Front_Action | Mage_Adminhtml_Controller_Action
class On4u_Gestitrans_Adminhtml_GestitransController extends Mage_Core_Controller_Front_Action
{
    public function envioAction()
    {

        $gestitrans = new On4u_Gestitrans_Model_On4u_Gestitrans();

        // User credentials
        $username = $gestitrans->getConfigData('username');
        $password = $gestitrans->getConfigData('password');

        // Api Url
        $urlPro = "https://www.gestitrans.com/service/rest/integration/bookPrivate/";
        $urlDes = "http://gestitrans.ibermatica.com/service/rest/integration/bookPrivate/";

        // Order data
        $order_id = $this->getRequest()->getParam('order');
        $order = Mage::getModel('sales/order')->load($order_id);

        $route_id = explode("_", $order->getShippingMethod()); // Ej: Gestitrans_86

        $hour = date("H");
        $weeksday = date("N");
        $pickUpDate = null;

        if($weeksday < 5 || ($weeksday == 5 && ($hour+1) < 13)) { // Antes del viernes a las 13PM
            if($hour+1 > 13) $pickUpDate = date('Y-m-d', strtotime(' +1 day'));
            else $pickUpDate = date('Y-m-d');
        }
        else $pickUpDate = date('Y-m-d', strtotime(' +3 day'));

        $customerBillingAddress = $order->getShippingAddress();

        $userData = Mage::getModel('Gestitrans/On4u_Source_User')->getUserData($username, $password);


        //Get bulklist
        $bulklist = $this->getBulks($gestitrans,$order);

        $region = Mage::getModel('directory/region')->load($gestitrans->getConfigData('region_id'));

        $bookPrivate = array(
            "accessCredentials" => array(
                "userName" => $username,
                "password" => $password
            ),
            "routeId" => "8946", //$route_id[1]
           "pickUpAddress" => array(
                "countryIso" => $gestitrans->getConfigData('country_id'),
                "postalCode" => $gestitrans->getConfigData('store_postcode'),
                "city" => utf8_encode($gestitrans->getConfigData('store_city')),
                "state" => utf8_encode($region->getName()),
                "address" => utf8_encode($gestitrans->getConfigData('store_address')),
                "contactName" => utf8_encode($gestitrans->getConfigData('store_contact_name')),
                "email" => $gestitrans->getConfigData('store_contact_email'),
                "companyName" => Mage::getStoreConfig('general/store_information/name'),
                "phoneNumber" => $gestitrans->getConfigData('store_contact_phone')
            ),
            "deliveryAddress" => array(
                "countryIso" =>$customerBillingAddress->getCountryId(),
                "postalCode" => $customerBillingAddress->getPostcode(),
                "city" => utf8_encode($customerBillingAddress->getCity()),
                "state" => utf8_encode($customerBillingAddress->getRegion()),
                "address" => utf8_encode($customerBillingAddress->getStreet()[0]),
                "contactName" => utf8_encode($customerBillingAddress->getName()),
                "email" => $customerBillingAddress->getEmail(),
                "companyName" => utf8_encode($customerBillingAddress->getName()),
                "phoneNumber" =>$customerBillingAddress->getTelephone()
            ),
            "pickUpDate" => $pickUpDate,
            "freightType" => "otras_mercancias_no_perecederas",
            "packaging" => "boxes_cardboard",
            "bulkList" => $bulklist,
            "schedulePickUp" => array(
                "horaIni" => substr($gestitrans->getConfigData('shedule_pick_up_morning_from'),0,2),
                "horaFin" => substr($gestitrans->getConfigData('shedule_pick_up_afternoon_to'),0,2),
                "horaPauseIni" => substr($gestitrans->getConfigData('shedule_pick_up_morning_to'),0,2),
                "horaPauseFin" => substr($gestitrans->getConfigData('shedule_pick_up_afternoon_from'),0,2),
            ),
            "scheduleDelivery" => array(
                "horaIni" =>substr($order->getShippingAddress()->getSheduleMorningFrom(),0,2),
                "horaFin" => substr($order->getShippingAddress()->getSheduleMorningTo(),0,2),
                "horaPauseIni" => substr($order->getShippingAddress()->getSheduleAfternoonFrom(),0,2),
                "horaPauseFin" => substr($order->getShippingAddress()->getSheduleAfternoonTo(),0,2)
            )
        );


/*  EXAMPLE WELL FORMED JSON:
      $bookPrivateOk = array(
            "accessCredentials" => array(
                "userName" => "l.macia@ibermatica.com",
                "password" => "aaAA11"
            ),
            "routeId"=>"8946",
            "pickUpAddress" => array(
                "countryIso" => "ES",
                "postalCode" => "20018",
                "city" => "San Sebastian",
                "state" => "Euskadi",
                "address" => "Miketegi 5",
                "contactName" => "Mikel Ibiricu",
                "email" => "jlumietu@gmail.com",
                "companyName" => "Ibermatica S.A.",
                "phoneNumber" => "943413500"
            ),
            "deliveryAddress" => array(
                "countryIso" =>"ES",
                "postalCode" => "43001",
                "city" => "Tarragona",
                "state" => "Catalunya",
                "address" => "Calle carrer",
                "contactName" => "Gerard",
                "email" => "m.ibiricu@ibermatica.com",
                "companyName" => "Ibermatica SA",
                "phoneNumber" =>"655730623"
            ),
            "pickUpDate"=> "2015-01-30",
            "freightType"=> "otras_mercancias_no_perecederas",
            "packaging"=> "boxes_cardboard",
            "bulkList" => array(
                array(
                    "lenght" => 15,
                    "width" => 16,
                    "height" => 20,
                    "weight" => 15.0,
                    "numeroIguales" => 2,
                    "tipoEmbalaje" => 1
                ),
            ),
            "schedulePickUp" => array(
                 "horaIni" => 9,
                 "horaFin" => 18,
                 "horaPauseIni" => 13,
                 "horaPauseFin" => 15,
             ),
            "scheduleDelivery" => array(
                "horaIni" => 8,
                "horaFin" => 17,
                "horaPauseIni" => 14,
                "horaPauseFin" => 15,
            )
    );*/

      $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/json",
                "Content-Type: application/json\r\n",
                'content' => json_encode($bookPrivate)
            )
        ));

        $response = file_get_contents($urlPro, false, $context);
        $data = json_decode($response, TRUE);

        //var_dump($data);
       //echo json_encode($bookPrivateOk);
        //die();


        /* @agoya-> Maybe use utf8_decode to get data from json */
        if($data['globalResult'] == "00"){

             //$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
             //$order->addStatusHistoryComment("El pedido ha sido enviado a traves del modulo de Gestitrans", Mage_Sales_Model_Order::STATE_COMPLETE);
             $order->setGestitransOrderId($data['orderId']);
             $order->setGestitransPickUpDate($data['pickupDate']);
             $order->save();

            Mage::getSingleton('core/session')->addSuccess("El envío se ha realizado correctamente. El order id asignado es el %s y la fecha de recogida en tienda el %s.", $data['orderId'],$data['pickupDate']);
            //Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$order_id)));
            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index"));

        }
        else{ /* Error with sincronization */
                    echo "Error with sincronization";
                    die();
                   /* Mage::getSingleton('adminhtml/session')->addError("No se ha podido realizar el envio por problemas de conexión con el API");
                    $this->_redirect('admin/sales_order/view', array('order_id'=>$order_id));*/



            //Mage::getSingleton('customer/session')->addError($this->__('The order has not been cancelled.'));
            //$this->_getSession()->addError("miau");
            //Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
         /*   $result = array();
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
            $result['redirect'] = 'http://infoenvia.local/index.php/admin/sales_order/index/key/72c916f0047cdbae55ecd9da54d1691f/';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));*/


            //echo $this->getUrl('*/sales_order/index');

            //$this->getResponse()->setRedirect($this->getUrl('*/sales_order/index'));
            //Mage::log('Miau', Zend_Log::INFO);
            //"No se ha podido realizar el envío por problemas de conexión con el API");

            Mage::getSingleton('adminhtml/session')
                ->init('core', 'adminhtml')
                ->addSuccess('Whoop');
            //$this->_redirect('*/sales_order/view', array('order_id' => $order_id));
            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index"));
            //die();
            //Mage::getSingleton('adminhtml/session')->addError("No se ha podido realizar el envío por problemas de conexión con el API");
            //Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/dashboard"));
            //$this->_redirect("/");
            //Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index"));





        }

    }

    public function getBulks($gestitrans,$order){
        $bulklist = array();

        $dimensions = explode('x', $gestitrans->getConfigData('box_dimensions'));
        $maxHeight = $gestitrans->getConfigData('max_weight');

        $length = $dimensions[0];
        $width = $dimensions[1];
        $height = $dimensions[2];

        $weightPerBulk= 0;
        $weight = 0;
        $totalItems = 0;
        $itemCounter = 0;
        $totalItemCounter = 0;
        $newWeight = 0;

        $ordered_items =$order->getAllItems();

        foreach($ordered_items as $item) {
            $qty = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyCanceled();
            $weight += $item->getWeight() * $qty;
            $totalItems += $qty ;
        }

        foreach ($ordered_items as $product) {
            $qty = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyCanceled();
            for ($count = 0; $count < $qty; $count++) {
                $weightPerBulk += $product->getWeight();
                $itemCounter++;
                $totalItemCounter++;

                if ($weightPerBulk >= $maxHeight) {
                    $newWeight = $weightPerBulk - $product->getWeight();
                    $bulk = array(
                        "lenght"=> $length,
                        "width"=> $width,
                        "height"=> $height,
                        "weight"=> $newWeight,
                        "numeroIguales"=> "2",
                        "tipoEmbalaje"=>"1"
                    );
                    array_push($bulklist,$bulk);
                    $weightPerBulk = $product->getWeight();
                }
                if ($totalItemCounter == $totalItems) {
                    $bulk = array(
                        "lenght"=> $length,
                        "width"=> $width,
                        "height"=> $height,
                        "weight"=> $newWeight,
                        "numeroIguales"=> "2",
                        "tipoEmbalaje"=>"1"
                    );
                    array_push($bulklist,$bulk);
                    $itemCounter = 0;
                    $weightPerBulk = 0.0;
                }
            }
        }

        return $bulklist;
    }

}
