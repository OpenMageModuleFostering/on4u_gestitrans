<?php
class On4U_Gestitrans_Model_Escoger
{
    public function toOptionArray()
    {
	return array(
            array('value'=>'todos', 'label'=>'Todos los transportistas permitidos'),
            array('value'=>'seleccion', 'label'=>'Especificar transportistas'),
        );
    }

}

