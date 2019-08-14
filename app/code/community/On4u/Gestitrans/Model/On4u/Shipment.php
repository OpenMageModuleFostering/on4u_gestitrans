<?php
class On4u_Gestitrans_Model_On4u_Shipment extends Mage_Sales_Model_Order_Shipment
{
    protected function _beforeSave()
    {
        $canSave = parent::_beforeSave();
        return $canSave;
    }
}

