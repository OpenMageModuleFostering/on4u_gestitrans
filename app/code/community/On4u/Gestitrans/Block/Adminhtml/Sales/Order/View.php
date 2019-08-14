<?php

class On4u_Gestitrans_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {
    public function  __construct() {

        parent::__construct();

        $state = $this->getOrder()->getState();
        $status = $this->getOrder()->getStatus();

        if (!$this->getOrder()->getGestitransOrderId() && ($state == 'new' || $state == 'pending_payment')){
            $this->_addButton('button_id', array(
                'label'     => Mage::helper('Page')->__('Private Book with Gestitrans'),
                'onclick' => "confirmSetLocation('Se contratará la gestion del envío a traves de Gestitrans', '" . Mage::getUrl('*/gestitrans/envio').'order/'.$this->getOrder()->getId() ."/key/c82a3c23806412bfe3581492e3e21500/')",
                'class'     => 'gestitrans'
            ), 0, 100, 'header', 'header');
        }

    }
}
