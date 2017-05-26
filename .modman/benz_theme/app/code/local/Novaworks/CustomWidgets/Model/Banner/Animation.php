<?php
class Novaworks_CustomWidgets_Model_Banner_Animation
{
    public function toOptionArray()
    {    

			return array(
				array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('None')),
            	array('value'=>'flipInX', 'label'=>Mage::helper('adminhtml')->__('flipInX')),
            	array('value'=>'flipInY', 'label'=>Mage::helper('adminhtml')->__('flipInY')),
            	array('value'=>'fadeIn', 'label'=>Mage::helper('adminhtml')->__('fadeIn')),
            	array('value'=>'fadeInUp', 'label'=>Mage::helper('adminhtml')->__('fadeInUp')),
            	array('value'=>'fadeInDown', 'label'=>Mage::helper('adminhtml')->__('fadeInDown')),
            	array('value'=>'fadeInLeft', 'label'=>Mage::helper('adminhtml')->__('fadeInLeft')),
            	array('value'=>'fadeInRight', 'label'=>Mage::helper('adminhtml')->__('fadeInRight')),
            	array('value'=>'fadeInUpBig', 'label'=>Mage::helper('adminhtml')->__('fadeInUpBig')),
            	array('value'=>'fadeInLeftBig', 'label'=>Mage::helper('adminhtml')->__('fadeInLeftBig')),
            	array('value'=>'fadeInRightBig', 'label'=>Mage::helper('adminhtml')->__('fadeInRightBig')),
            	array('value'=>'bounceIn', 'label'=>Mage::helper('adminhtml')->__('bounceIn')),
            	array('value'=>'bounceInUp', 'label'=>Mage::helper('adminhtml')->__('bounceInUp')),
            	array('value'=>'bounceInDown', 'label'=>Mage::helper('adminhtml')->__('bounceInDown')),
            	array('value'=>'bounceInLeft', 'label'=>Mage::helper('adminhtml')->__('bounceInLeft')),
            	array('value'=>'bounceInRight', 'label'=>Mage::helper('adminhtml')->__('bounceInRight')),
            	array('value'=>'rotateInUpLeft', 'label'=>Mage::helper('adminhtml')->__('rotateInUpLeft')),
            	array('value'=>'rotateInUpRight', 'label'=>Mage::helper('adminhtml')->__('rotateInUpRight')),
            	array('value'=>'rotateInDownRight', 'label'=>Mage::helper('adminhtml')->__('rotateInDownRight'))
            	
        	);
    } 
}
