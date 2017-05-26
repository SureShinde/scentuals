/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     rwd_default
 * @copyright   Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var ConfigurableMediaImages = {
    productImages: {},
    imageObjects: {},

    arrayIntersect: function(a, b) {
        var ai=0, bi=0;
        var result = new Array();

        while( ai < a.length && bi < b.length )
        {
            if      (a[ai] < b[bi] ){ ai++; }
            else if (a[ai] > b[bi] ){ bi++; }
            else /* they're equal */
            {
                result.push(a[ai]);
                ai++;
                bi++;
            }
        }

        return result;
    },

    getCompatibleProductImages: function(productFallback, selectedLabels) {
        //find compatible products
        var compatibleProducts = [];
        var compatibleProductSets = [];
        selectedLabels.each(function(selectedLabel) {
            if(!productFallback['option_labels'][selectedLabel]) {
                return;
            }

            var optionProducts = productFallback['option_labels'][selectedLabel]['products'];
            compatibleProductSets.push(optionProducts);

            //optimistically push all products
            optionProducts.each(function(productId) {
                compatibleProducts.push(productId);
            });
        });

        //intersect compatible products
        compatibleProductSets.each(function(productSet) {
            compatibleProducts = ConfigurableMediaImages.arrayIntersect(compatibleProducts, productSet);
        });

        return compatibleProducts;
    },

    isValidImage: function(fallbackImageUrl) {
        if(!fallbackImageUrl) {
            return false;
        }
        return true;
    },

    getSwatchImage: function(productId, optionLabel, selectedLabels , type_image) {
        var fallback = ConfigurableMediaImages.productImages[productId];
        if(!fallback) {
            return null;
        }

        //first, try to get label-matching image on config product for this option's label
        var currentLabelImage = fallback['option_labels'][optionLabel];

        if(currentLabelImage && fallback['option_labels'][optionLabel]['configurable_product'][type_image]) {
            //found label image on configurable product
            return fallback['option_labels'][optionLabel]['configurable_product'][type_image];
        }

        var compatibleProducts = ConfigurableMediaImages.getCompatibleProductImages(fallback, selectedLabels);


        if(compatibleProducts.length == 0) { //no compatible products
            return null; //bail
        }

        //second, get any product which is compatible with currently selected option(s)
        jQuery.each(fallback['option_labels'], function(key, value) {
            var image = value['configurable_product'][type_image];
            var products = value['products'];

            if(image) { //configurable product has image in the first place
                //if intersection between compatible products and this label's products, we found a match
                var isCompatibleProduct = ConfigurableMediaImages.arrayIntersect(products, compatibleProducts).length > 0;
                if(isCompatibleProduct) {
                    return image;
                }
            }
        });

        //third, get image off of child product which is compatible
        var childSwatchImage = null;
        var childProductImages = fallback[type_image];

        compatibleProducts.each(function(productId) {
            if(childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
                childSwatchImage = childProductImages[productId];
                return false; //break "loop"
            }
        });
        if (childSwatchImage) {
            return childSwatchImage;
        }

        //fourth, get base image off parent product
        if (childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
            return childProductImages[productId];
        }

        //no fallback image found
        return null;
    },

    checkImageLoaded : function (image_url,callback){
        if(!ConfigurableMediaImages.imageObjects[image_url]) {
            var image = jQuery('<img />');
            image.attr('src', image_url);
            imagesLoaded(image,function(){
                ConfigurableMediaImages.imageObjects[image_url] = image_url;
            })
            callback(image_url);
        }else{
            callback(image_url);
        }
    },

    updateImageDetail : function(el){
        var select = jQuery(el);
        var label = select.find('option:selected').attr('data-label');
        var productId = optionsPrice.productId; //get product ID from options price object
        //find all selected labels
        var selectedLabels = new Array();

        jQuery('.product-options .super-attribute-select').each(function() {
            var $option = jQuery(this);
            if($option.val() != '') {
                selectedLabels.push($option.find('option:selected').attr('data-label'));
            }
        });
        var base_image = ConfigurableMediaImages.getSwatchImage(productId, label, selectedLabels ,'base_image'),
            detail_image_zoom = ConfigurableMediaImages.getSwatchImage(productId, label, selectedLabels ,'detail_image3x'),
            detail_image_thumb = ConfigurableMediaImages.getSwatchImage(productId, label, selectedLabels ,'detail_image');

        if(!ConfigurableMediaImages.isValidImage(base_image)){
            return;
        }
        if(!ConfigurableMediaImages.isValidImage(detail_image_zoom) && !ConfigurableMediaImages.isValidImage(detail_image_thumb)){
            detail_image_zoom = base_image;
            detail_image_thumb = base_image;
        }

        ProductMediaManager.swapImageDetail(base_image,detail_image_zoom,detail_image_thumb);
    },

    updateImageList : function(image_url,$product){
        ProductMediaManager.swapImageList(image_url,$product);
    },

    wireOptions: function() {
        jQuery('.product-options .super-attribute-select').change(function(e) {
            ConfigurableMediaImages.updateImageDetail(this);
        });
    },

    swapListImageByOption: function(productId, optionLabel , $product , image_type) {
        var swatchImageUrl = ConfigurableMediaImages.getSwatchImage(productId, optionLabel, [optionLabel] ,image_type);
        if(!swatchImageUrl) {
            return;
        }
        ConfigurableMediaImages.updateImageList(swatchImageUrl,$product);
    },

    setImageFallback: function(productId, imageFallback) {
        ConfigurableMediaImages.productImages[productId] = imageFallback;
    },

    init: function() {
        ConfigurableMediaImages.wireOptions();
    }
};
