<?php

class Collinsharper_Canpost_Block_Adminhtml_System_Config_Signup
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    
    /**
     * Set template to itself
     */
    protected function _prepareLayout()
    {
        
        parent::_prepareLayout();
        
        if (!$this->getTemplate()) {
            
            $this->setTemplate('canpost/system/config/signup.phtml');
            
        }
        
        return $this;
        
    }

    
    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        
        return parent::render($element);
        
    }

    
    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        
        $originalData = $element->getOriginalData();

	$urlPar = array();

	$store = $this->getRequest()->getParam('store', '');

	$website = $this->getRequest()->getParam('website', '');

	if (!empty($store)) {

		$urlPar['store'] = $store;

	}

	if (!empty($website)) {

		$urlPar['website'] = $website;
	
	}

        $this->addData(array(
            'button_label' => Mage::helper('chcanpost2module')->__($originalData['button_label']),
            'html_id' => $element->getHtmlId(),
            'endpoint' => $this->getUrl('adminhtml/signup/update', $urlPar)
        ));
        
        return $this->_toHtml();
        
    }
    
}
