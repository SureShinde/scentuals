<?php

class Collinsharper_Canpost_Model_System_Config_Source_Customer_Group
{
    protected $_options;

    public function toOptionArray()
    {
        if ($this->_options) {
            return $this->_options;
        }

        $this->_options = Mage::getResourceModel('customer/group_collection')
            ->setRealGroupsFilter()
            ->loadData()
            ->toOptionArray();

        $foundGuestGroup = false;
        foreach ($this->_options as $group) {
            if ($group['value'] == 0) {
                $foundGuestGroup = true;
                break;
            }
        }

        if (!$foundGuestGroup) {
            array_unshift($this->_options, array(
                'value' => 0,
                'label' => Mage::helper('adminhtml')->__("NOT LOGGED IN")
            ));
        }

        return $this->_options;
    }
}
