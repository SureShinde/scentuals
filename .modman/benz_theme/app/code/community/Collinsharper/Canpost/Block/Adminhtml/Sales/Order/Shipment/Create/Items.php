<?php
/**
 *
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman
 */
class Collinsharper_Canpost_Block_Adminhtml_Sales_Order_Shipment_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items
{


    /**
     * Prepare child blocks
     */
    protected function _beforeToHtml()
    {
        $onclick = 'submitShipment(this);console.log("changed");';
        if (version_compare(Mage::getVersion(), '1.6.0.0', '<')) {
            $onclick = 'if(editForm.submit()) disableElements(\'submit-button\');';
        }

        $this->setChild(
            'submit_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('chcanpost2module')->__('Approve Shipment'),
                'class'     => 'save submit-button',
                'onclick'   => 'submitShipment(this);',
            ))
        );

    }

}
