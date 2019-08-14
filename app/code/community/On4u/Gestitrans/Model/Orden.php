<?php
class On4U_Gestitrans_Model_Orden
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'precio', 'label'=> 'Precio'),
            array('value'=>'tiempo', 'label'=> 'Tiempo estimado'),
        );
    }

}

