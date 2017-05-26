<?php

/* * ****************************************************
 * Package   : Brand
 * Author    : ArexWorks
 * Copyright : (c) 2015
 * ***************************************************** */
?>
<?php

class ARW_Brand_Block_Layer_View extends Mage_Catalog_Block_Layer_View
{

    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer(), true);
    }

    public function getLayer()
    {
        return Mage::getSingleton('brand/layer');
    }

}
