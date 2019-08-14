<?php
class On4u_Gestitrans_Model_Observer{

    public function saveQuoteBefore($evt){
        //Mage::log("Entrando en saveQuoteBefore");
        $quote = $evt->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $post = Mage::app()->getFrontController()->getRequest()->getPost();

        if(isset($post['shedule_morning_from_hour'])){
            $shippingAddress->setSheduleMorningFrom($post['shedule_morning_from_hour']);
            $shippingAddress->setSheduleMorningTo($post['shedule_morning_to_hour']);
            $shippingAddress->setSheduleAfternoonFrom($post['shedule_afternoon_from_hour']);
            $shippingAddress->setSheduleAfternoonTo($post['shedule_afternoon_to_hour']);
            $shippingAddress->save();
        }
        //Mage::log("Saliendo de saveQuoteBefore");
        return $this;
    }

    public function saveOrderAfter($observer){
        //Mage::log("Entrando en saveOrderAfter");
        $action = Mage::app()->getFrontController()->getAction();
        $name = $action->getFullActionName();
        if($name == "checkout_onepage_saveOrder"){
            $event = $observer->getEvent();
            $order = $event->getOrder();
            $quote = $event->getOrder()->getQuote();
            $quoteSA = $quote->getShippingAddress();
            $orderSA = $order->getShippingAddress();
            if($quoteSA->getSheduleMorningFrom() != null){
                $orderSA->setSheduleMorningFrom($quoteSA->getSheduleMorningFrom());
                $orderSA->setSheduleMorningTo($quoteSA->getSheduleMorningTo());
                $orderSA->setSheduleAfternoonFrom($quoteSA->getSheduleAfternoonFrom());
                $orderSA->setSheduleAfternoonTo($quoteSA->getSheduleAfternoonTo());
                $orderSA->save();
            }
        }
        //Mage::log("Saliendo de saveOrderAfter");
        return $this;
    }


}
