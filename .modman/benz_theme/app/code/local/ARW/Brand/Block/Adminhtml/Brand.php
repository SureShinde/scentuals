<?php

/* * ****************************************************
 * Package   : Brand
 * Author    : ArexWorks
 * Copyright : (c) 2015
 * ***************************************************** */
?>
<?php

class ARW_Brand_Block_Adminhtml_Brand extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_brand';
        $this->_blockGroup = 'brand';
        $this->_headerText = Mage::helper('brand')->__('Manage Brands');
        $this->_addButtonLabel = Mage::helper('brand')->__('Add New Brand');
        $this->_addButton('refresh', array(
            'label' => 'Refresh Url Rewrite',
            'onclick' => 'setLocation(\'' . $this->getRefreshUrl() . '\')',
            'class' => 'scalable',
        ));
        parent::__construct();
    }

    public function getRefreshUrl()
    {
        return $this->getUrl('*/*/refreshUrl');
    }

}
