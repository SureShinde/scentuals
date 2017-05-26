<?php

class ARW_Sebian_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_patternPath;
    protected $_bgImagesPath;
    public function __construct()
    {
        //Create paths
        $this->_patternPath     = 'wysiwyg/arw/sebian/_patterns/';
        $this->_bgImagesPath    = 'wysiwyg/arw/sebian/_backgrounds/';
    }
    public function getBgImagesPath()
    {
        return $this->_bgImagesPath;
    }
    public function getPatternPath()
    {
        return $this->_patternPath;
    }
    public function getCfg($optionString,$storeCode = NULL)
    {
        $storeCode = Mage::registry('cssgen_store');
        return Mage::getStoreConfig('sebian/' . $optionString , $storeCode);
    }
    public function getCfgDesign($optionString)
    {
        return Mage::getStoreConfig('sebian_design/' . $optionString);
    }
    public function getCfgLayout($optionString)
    {
        return Mage::getStoreConfig('sebian_layout/' . $optionString);
    }
    public function getSliderId($optionString)
    {
        return $this->getCfg('header/sliderev');
    }
    public function getCfgSectionDesign($storeId)
    {
        if ($storeId){
            return Mage::getStoreConfig('sebian_design', $storeId);
        }
        else{
            return Mage::getStoreConfig('sebian_design');
        }
    }
    public function getCfgSectionLayout($storeId)
    {
        if ($storeId){
            return Mage::getStoreConfig('sebian_layout', $storeId);
        }
        else{
            return Mage::getStoreConfig('sebian_layout');
        }
    }
    public function getThemeDesignCfg($optionString, $storeCode = NULL)
    {
        return Mage::getStoreConfig('sebian_design/' . $optionString, $storeCode);
    }
    public function getThemeLayoutCfg($optionString, $storeCode = NULL)
    {
        return Mage::getStoreConfig('sebian_layout/' . $optionString, $storeCode);
    }

    public function getConfig($optionString,$storeCode = null){
        return Mage::getStoreConfig($optionString, $storeCode);
    }


    protected function _loadProduct(Mage_Catalog_Model_Product $product)
    {
        $product->load($product->getId());
    }
    public function getLabel(Mage_Catalog_Model_Product $product)
    {
        if ( 'Mage_Catalog_Model_Product' != get_class($product) )
            return;
        $html = '';
        if (!$this->getCfg("product_labels/label")) {
            return $html;
        }
        $this->_loadProduct($product);
        $array_label=explode(',',$this->getCfg("product_labels/label"));
        if (( in_array('new',$array_label) && $this->_checkNew($product) ) || ( in_array('sale',$array_label) && $this->_checkSale($product) )) {
            $html .= '<div class="arw-product-labels">';
        }
        if ( in_array('new',$array_label) && $this->_checkNew($product) ) {
            $html .= '<span class="arw-product-label product-new-label">'.$this->__('New').'</span>';
        }
        if ( in_array('sale',$array_label) && $this->_checkSale($product) ) {
            $percent = 100 - round(($product->getFinalPrice() / $product->getPrice()) * 100);
            $html .= '<span class="arw-product-label product-sale-label"><span>'.$this->__('Sale').'</span></span>';
        }
        if (( in_array('new',$array_label) && $this->_checkNew($product) ) || ( in_array('sale',$array_label) && $this->_checkSale($product) )) {
            $html .= '</div>';
        }
        return $html;
    }
    protected function _checkDate($from, $to)
    {
        $today = strtotime(
            Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
        );
        if ($from && $today < $from) {
            return false;
        }
        if ($to && $today >= $to) {
            return false;
        }
        if (!$to && !$from) {
            return false;
        }
        return true;
    }
    protected function _checkNew($product)
    {
        $from = strtotime($product->getData('news_from_date'));
        $to = strtotime($product->getData('news_to_date'));

        return $this->_checkDate($from, $to);
    }
    protected function _checkSale($product)
    {
        if($product->getPrice() > $product->getFinalPrice()){
            $from = strtotime($product->getData('special_from_date'));
            $to = strtotime($product->getData('special_to_date'));

            return $this->_checkDate($from, $to);
        }else{
            return false;
        }
    }

    public function show_product_countdown($product){
        if($product->getPrice() > $product->getFinalPrice()){
            $from = strtotime($product->getData('special_from_date'));
            $to = strtotime($product->getData('special_to_date'));
            if($to && $this->_checkDate($from, $to)){
                $date = date("m/d/Y h:i:s",$to);
                $now = date("m/d/Y h:i:s");
                return '<div class="arw-countdown-for-product" data-jcdData="'.$now.'" data-cdate="'.$date.'"></div>';
            }
        }
    }

    public function hex2rgba($hex,$opacity) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgba = array($r, $g, $b,$opacity);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        $css  = "rgba(" . implode(",",$rgba) . ")";
        return $css; // returns an array with the rgb values
    }
    public function isColor($color)
    {
        if ($color && $color != 'transparent')
            return true;
        else
            return false;
    }

    public function addBodyClass(){
        $classes = array();
        $body_style = $this->getCfgLayout('layout/style') ? $this->getCfgLayout('layout/style') : 'stretched';
        $sidebar_style = $this->getCfg('general/sidebar_mobile') ? $this->getCfg('general/sidebar_mobile') :  'hide';
        $grid_style = $this->getCfg('category_grid/style') ? $this->getCfg('category_grid/style') : 'style_1';

        $classes[] = 'sidebar-mobile-'.$sidebar_style;
        $classes[] = 'body-'.$body_style;
        if($grid_style == 'style_2'){
            $classes[] = 'grid-style-2';
        }
        return implode(' ',$classes);
    }

    public function renderOptionDropdownCategory($_categories = null , $max_depth = false , $depth = 0 , $output = '' ,$repeat = "-" ,$current = false){
        if (count($_categories) > 0){
            $depth++;
            foreach($_categories as $_category){
                $_category = Mage::getModel('catalog/category')->load($_category->getId());
                $_subcategories = $_category->getChildrenCategories();
                $html = "<option";
                $html .= " value='{$_category->getId()}'";
                if($current == $_category->getId()){
                    $html .= " selected='selected'";
                }
                $html .= ">";
                if($depth > 1){
                    $html .= str_repeat($repeat,$depth-1);
                }
                $html .= $_category->getName();
                $html .= "</option>";
                if(($max_depth > $depth || !$max_depth) && count($_subcategories) > 0){
                    $output .= $this->renderOptionDropdownCategory($_subcategories,$max_depth,$depth,$html,$repeat,$current);
                }else{
                    $output .= $html;
                }
            }
            return $output;
        }else{
            return;
        }
    }
    public function resizeImage($fileName, $width = '', $height = '',$keepAspectRatio = false , $keepFrame = false)
    {
        $folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $imageURL = $folderURL . $fileName;

        $basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $fileName;

        if ($width != '') {
            $fileNameTmp = explode(DS,$fileName);
            $folderName = "catalog" . DS . "product". DS . "cache" . DS . "blog" . DS . $width .($height ? 'x'.$height : ''). DS . $fileNameTmp[count($fileNameTmp) - 1];
            $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $folderName;
            if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
                $_image = new Varien_Image($basePath);
                $_image->keepAspectRatio($keepAspectRatio);
                $_image->keepFrame($keepFrame);
                $_image->quality(100);
                $_image->resize($width, $height);
                $_image->save($newPath);
            }
            $resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $folderName;
        } else {
            $resizedURL = $imageURL;
        }

        return $resizedURL;
    }

    public function getProductImageFallbacks($products = null,$keepFrame = null , $imageTypes = null) {
        if($imageTypes === null){
            //array('image','small_image','grid_image','list_image','detail_image','detail_image3x')
            $imageTypes = array('image','detail_image','detail_image3x');
        }
        $fallbacks = array();
        if($products === null){
            return $fallbacks;
        }
        if ($keepFrame === null) {
            $listBlock = $this->getLayout()->getBlock('product_list');
            if ($listBlock && $listBlock->getMode() == 'grid') {
                $keepFrame = true;
            } else {
                $keepFrame = false;
            }
        }
        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            if($product->getTypeId() === 'configurable'){
                $imageFallback = $this->getConfigurableImagesFallbackArray($product, $imageTypes, $keepFrame);
                $fallbacks[$product->getId()] = array(
                    'product' => $product,
                    'image_fallback' => str_replace('\"','',Mage::helper('core')->jsonEncode($imageFallback))
                );
            }
        }

        return $fallbacks;
    }
    public function getConfigurableImagesFallbackArray(Mage_Catalog_Model_Product $product, array $imageTypes,
                                                       $keepFrame = false
    ) {
        if(class_exists('Mage_ConfigurableSwatches_Helper_Data')){
            if (!$product->hasConfigurableImagesFallbackArray()) {
                $mapping = $product->getChildAttributeLabelMapping();

                $mediaGallery = $product->getMediaGallery();

                if (!isset($mediaGallery['images'])) {
                    return array(); //nothing to do here
                }

                // ensure we only attempt to process valid image types we know about
                $imageTypes = array_intersect(array('image', 'small_image','grid_image','list_image','detail_image','detail_image3x'), $imageTypes);

                $imagesByLabel = array();
                $imageHaystack = array_map(function ($value) {
                    return Mage_ConfigurableSwatches_Helper_Data::normalizeKey($value['label']);
                }, $mediaGallery['images']);

                // load images from the configurable product for swapping
                foreach ($mapping as $map) {
                    $imagePath = null;

                    //search by store-specific label and then default label if nothing is found
                    $imageKey = array_search($map['label'], $imageHaystack);
                    if ($imageKey === false) {
                        $imageKey = array_search($map['default_label'], $imageHaystack);
                    }

                    //assign proper image file if found
                    if ($imageKey !== false) {
                        $imagePath = $mediaGallery['images'][$imageKey]['file'];
                    }

                    $imagesByLabel[$map['label']] = array(
                        'configurable_product' => array(
                            Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => null,
                            Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => null,
                            'list_image' => null,
                            'grid_image' => null,
                            'detail_image' => null,
                            'detail_image3x' => null
                        ),
                        'products' => $map['product_ids'],
                    );

                    if ($imagePath) {
                        $imagesByLabel[$map['label']]['configurable_product']
                        [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL] =
                            $this->_resizeProductImage($product, 'small_image', $keepFrame, $imagePath);

                        $imagesByLabel[$map['label']]['configurable_product']
                        [Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE] =
                            $this->_resizeProductImage($product, 'image', $keepFrame, $imagePath);

                        $imagesByLabel[$map['label']]['configurable_product']['list_image'] =
                            $this->_resizeProductImage($product, 'list_image', $keepFrame, $imagePath);
                        $imagesByLabel[$map['label']]['configurable_product']['grid_image'] =
                            $this->_resizeProductImage($product, 'grid_image', $keepFrame, $imagePath);
                    }
                }

                $imagesByType = array(
                    'image' => array(),
                    'grid_image' => array(),
                    'list_image' => array(),
                    'small_image' => array(),
                    'detail_image' => array(),
                    'detail_image3x'=> array()
                );
                // iterate image types to build image array, normally one type is passed in at a time, but could be two
                foreach ($imageTypes as $imageType) {
                    // load image from the configurable product's children for swapping
                    /* @var $childProduct Mage_Catalog_Model_Product */
                    if ($product->hasChildrenProducts()) {
                        foreach ($product->getChildrenProducts() as $childProduct) {
                            if ($image = $this->_resizeProductImage($childProduct, $imageType, $keepFrame)) {
                                $imagesByType[$imageType][$childProduct->getId()] = $image;
                            }
                        }
                    }

                    // load image from configurable product for swapping fallback
                    if ($image = $this->_resizeProductImage($product, $imageType, $keepFrame, null, true)) {
                        $imagesByType[$imageType][$product->getId()] = $image;
                    }
                }

                $array = array(
                    'option_labels' => $imagesByLabel,
                    'grid_image' => $imagesByType['grid_image'],
                    'list_image' => $imagesByType['list_image'],
                    'detail_image' => $imagesByType['detail_image'],
                    'detail_image3x' => $imagesByType['detail_image3x'],
                    Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_SMALL => $imagesByType['small_image'],
                    Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE => $imagesByType['image'],
                );

                $product->setConfigurableImagesFallbackArray($array);
            }
            return $product->getConfigurableImagesFallbackArray();
        }else{
            return array();
        }
    }

    protected function _resizeProductImage($product, $type, $keepFrame = true, $image = null, $placeholder = false)
    {
        $type_tmp = (in_array($type,array('grid_image','list_image','detail_image','detail_image3x'))) ? 'small_image' : $type;
        $hasTypeData = $product->hasData($type_tmp) && $product->getData($type_tmp) != 'no_selection';
        if ($image == 'no_selection') {
            $image = null;
        }

        if ($hasTypeData || $placeholder || $image) {
            $helper = Mage::helper('catalog/image')
                ->init($product, $type_tmp, $image);
            if(in_array($type,array('grid_image','list_image'))){
                $helper->keepAspectRatio($keepFrame);
            }
            $_w = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_BASE_IMAGE_WIDTH);
            $_h = null;
            if ($type == 'small_image') {
                $_w = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_SMALL_IMAGE_WIDTH);
            }
            if ($type == 'grid_image') {
                $_w = $this->getCfg('category/image_width');
                $_h = $this->getCfg('category/image_height');
            }
            if ($type == 'detail_image') {
                $_w = $this->getCfg('product_detail_zoom/image_main_width');
                $_h = $this->getCfg('product_detail_zoom/image_main_height');
            }
            if ($type == 'detail_image3x') {
                $_w = $this->getCfg('product_detail_zoom/image_main_width') * 3;
                $_h = $this->getCfg('product_detail_zoom/image_main_height') * 3;
            }
            if ($type == 'list_image') {
                $_w = $this->getCfg('category/image_width');
                $_h = $this->getCfg('category/image_height');
            }
            if($type !='image'){
                $helper->resize($_w,$_h);
            }
            return (string)$helper;
        }
    }
}