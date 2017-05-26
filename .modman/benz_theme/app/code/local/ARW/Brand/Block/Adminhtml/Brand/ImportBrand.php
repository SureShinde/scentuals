<?php

class ARW_Brand_Block_Adminhtml_Brand_importBrand extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('arw/brand/importBrand.phtml');
    }
}