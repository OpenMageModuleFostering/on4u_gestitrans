<?php
class On4U_Gestitrans_Model_Margen
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'porcentaje', 'label'=> 'Porcentaje (%)'),
            array('value'=>'fijo', 'label'=> 'Valor fijo'),
        );
    }

}

