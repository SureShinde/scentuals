<?php

class Collinsharper_Canpost_Model_Source_Regionus
{
    protected $_options;

    public function toOptionArrayCa($isMultiselect = false)
    {
        return $this->toOptionArray($isMultiselect, 'CA');
    }

    public function toOptionArrayUs($isMultiselect = false)
    {
        return $this->toOptionArray($isMultiselect, 'US');
    }

    public function toOptionArray($isMultiselect = false)
    {
        return $this->toOptionArrayCcountry($isMultiselect, 'US');
    }

    public function toOptionArrayCcountry($isMultiselect = false, $country = 'US')
    {
        if (!$this->_options || !isset($this->_options[$country])) {
            $this->_options[$country] = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($country)
                ->load();
        }

        $options = array();
        foreach ($this->_options[$country] as $o) {
            $options[] = array('value'=> $o->getCode(), 'label'=> $o->getDefaultName());
        }

        if (!$isMultiselect) {
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}
