<?php

class ARW_ProductTab_Block_Adminhtml_Producttab_ImportTab extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('arw/producttab/import_tab.phtml');
    }
}