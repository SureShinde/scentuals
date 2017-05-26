<?php

class ARW_ArexWorks_Helper_Image extends Mage_Core_Helper_Abstract
{
	public function getImg($product, $w, $h, $imgVersion='image', $file=NULL)
	{
		$url = '';
		if ($h <= 0)
		{
			$url = Mage::helper('catalog/image')
				->init($product, $imgVersion, $file)
				->constrainOnly(true)
				->keepAspectRatio(true)
				->keepFrame(false)
				->resize($w);
		}
		else
		{
			$url = Mage::helper('catalog/image')
				->init($product, $imgVersion, $file)
				->resize($w, $h);
		}
		return $url;
	}
}
