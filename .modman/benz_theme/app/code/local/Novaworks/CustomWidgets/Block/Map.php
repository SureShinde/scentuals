<?php
class Novaworks_CustomWidgets_Block_Map extends Mage_Core_Block_Template
{
    /**
     * Set default template.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setData('template', 'novaworks/custom_widgets/map.phtml');
        parent::_construct();
    }

    /**
     * Return absolute url to the image file.
     *
     * @return string
     */
    public function getPinUrl()
    {
        return Mage::getBaseUrl('media') . $this->getCustomMapPin();
    }
}