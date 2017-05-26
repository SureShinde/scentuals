<?php 
class ARW_ProductTab_Helper_Data extends Mage_Core_Helper_Abstract
{
		const PRODUCT_FORM_DATA = 'product_form_data';
		const XML_ENABLE_TAB						=	'producttab/general/enabled';
		const XML_GENERAL_EFFECT					=	'producttab/general/effect';
		const XML_GENERAL_DURATION_SLIDE			=	'producttab/general/duration_slide';
		const XML_GENERAL_DURATION_FADE				=	'producttab/general/duration_fade';
		const XML_GENERAL_FROM_FADE					=	'producttab/general/from';
		const XML_GENERAL_TO_FADE					=	'producttab/general/to';
		const XML_GENERAL_SCALEFROM_SLIDE			=	'producttab/general/scaleFrom';
		const XML_GENERAL_SCALETO_SLIDE				=	'producttab/general/scaleTo';
		const XML_GENERAL_SCAlEX_SLIDE				=	'producttab/general/scaleX';
		const XML_GENERAL_SCAlEY_SLIDE				=	'producttab/general/scaleY';
		const XML_GENERAL_DIRECTION_SLIDE			=	'producttab/general/direction';
	    const XML_CATEGORYSLIDER_SLIDESHOW_LIMIT	=	'producttab/slideshow/limit';
		const XML_GENERAL_AJAX_LOADING				=	'producttab/general/loading_icon';
		const XML_GENERAL_ENABLE_JQUERY				=	'producttab/general/enabled_jquery';
		const XML_GENERAL_ENABLE_BOOSTTRAP			=	'producttab/general/enabled_bootstrap';
	public function getEnableJquery()
	{
			return Mage::getStoreConfig(self::XML_GENERAL_ENABLE_JQUERY);
	}
	public function getEnableBootstrap()
	{
			return Mage::getStoreConfig(self::XML_GENERAL_ENABLE_BOOSTTRAP);
	}
	public function getEnableModule()
	{
			return Mage::getStoreConfig(self::XML_ENABLE_TAB);
	}
	public function getUrlIconLoading()
	{
		$img = Mage::getStoreConfig(self::XML_GENERAL_AJAX_LOADING);
		$url= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'arw/producttab/ajax/'.$img;
		return $url;	
	}		
	public function getCf($conf)
	{
		return Mage::getStoreConfig('producttab/slideshow/'.$conf);
	}
	public function getLimit()
	{
			return Mage::getStoreConfig(self::XML_CATEGORYSLIDER_SLIDESHOW_LIMIT);
	}
	public function getEffect() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_EFFECT);
	}
	public function getDurationSlide() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_DURATION_SLIDE);
	}
	public function getDurationFade() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_DURATION_FADE);
	}
	public function getFromFade() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_FROM_FADE);
	}
	public function getToFade() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_TO_FADE);
	}
	public function getScaleFromSlide() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_SCALEFROM_SLIDE);
	}
	public function getScaleToSlide() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_SCALETO_SLIDE);
	}
	public function getScaleXSlide() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_SCAlEX_SLIDE);
	}
	public function getScaleYSlide() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_SCAlEY_SLIDE);
	}
	public function getDirection() 
	{
		return Mage::getStoreConfig(self::XML_GENERAL_DIRECTION_SLIDE);
	}
	public function checkVersion($version)
    {
        return version_compare(Mage::getVersion(), $version, '>=');
    }
	 public function removeEmptyItems($var)
    {
        return !empty($var);
    }

    public function prepareArray($var)
    {
        if (is_string($var)) {
            $var = @explode(',', $var);
        }
        if (is_array($var)) {
            $var = array_unique($var);
            $var = array_filter($var, array(Mage::helper('producttab'), 'removeEmptyItems'));
            $var = @implode(',', $var);
        }
        return $var;
    }
	public function jsParam($obj)
   {
   /*  var_dump($obj->getData()) */
       $param = array(
			'url'				=>	$obj->getSendUrl(),
			'effect'           	=>  $this->getEffect(),
            'duration'        	=>  ($this->getEffect()=="slide")? $this->getDurationSlide():(($this->getEffect()=="fade")?$this->getDurationFade():""), 
			'from'				=>	($this->getEffect()=="slide")? $this->getScaleFromSlide():(($this->getEffect()=="fade")?$this->getFromFade():""), 
			'to'				=>	($this->getEffect()=="slide")? $this->getScaleToSlide():(($this->getEffect()=="fade")?$this->getToFade():""),
			'direction'			=>	$this->getDirection()==0? 'true':'false',	
			'identifier'		=>	$obj->getConfig('identifier'),
			'icon'				=>	$this->getUrlIconLoading(),
			'is_product_view_tab'    =>  Mage::registry('current_product') ? 1 : 0
       );      
       return Zend_Json::encode($param);
   }
}
?>