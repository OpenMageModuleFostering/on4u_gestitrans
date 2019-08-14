<?php

/* @var $installer Mage_Sales_Model_Resource_Setup */
/*$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$entities = array(
    'quote_address',
    'order_address',
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'visible'  => true,
    'required' => false
);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'shedule_morning_from', $options);
    $installer->addAttribute($entity, 'shedule_morning_to', $options);
    $installer->addAttribute($entity, 'shedule_afternoon_from', $options);
    $installer->addAttribute($entity, 'shedule_afternoon_to', $options);
}
$installer->endSetup();*/

/* @var $installer Mage_Sales_Model_Entity_Setup */
//$installer = $this;

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->startSetup();

$entities = array(
    'quote_address',
    'order_address',
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR, // VARCHAR (Mage_Sales_Model_Resource_Setup), TEXT(Mage_Sales_Model_Entity_Setup)
    'visible'  => true,
    'required' => false
);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'shedule_morning_from', $options);
    $installer->addAttribute($entity, 'shedule_morning_to', $options);
    $installer->addAttribute($entity, 'shedule_afternoon_from', $options);
    $installer->addAttribute($entity, 'shedule_afternoon_to', $options);
}

$installer->addAttribute('order', 'gestitrans_order_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'visible'  => true,
        'required' => false
    ));

$installer->addAttribute('order', 'gestitrans_pick_up_date', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'visible'  => true,
        'required' => false

    ));

$installer->endSetup();
