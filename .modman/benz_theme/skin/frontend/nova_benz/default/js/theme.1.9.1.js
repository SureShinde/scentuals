var bp = {
    xsmall: 479,
    small: 599,
    medium: 770,
    large: 979,
    xlarge: 1199
};
var PointerManager = {
    MOUSE_POINTER_TYPE: 'mouse',
    TOUCH_POINTER_TYPE: 'touch',
    POINTER_EVENT_TIMEOUT_MS: 500,
    standardTouch: false,
    touchDetectionEvent: null,
    lastTouchType: null,
    pointerTimeout: null,
    pointerEventLock: false,

    getPointerEventsSupported: function() {
        return this.standardTouch;
    },

    getPointerEventsInputTypes: function() {
        if (window.navigator.pointerEnabled) { //IE 11+
            //return string values from http://msdn.microsoft.com/en-us/library/windows/apps/hh466130.aspx
            return {
                MOUSE: 'mouse',
                TOUCH: 'touch',
                PEN: 'pen'
            };
        } else if (window.navigator.msPointerEnabled) { //IE 10
            //return numeric values from http://msdn.microsoft.com/en-us/library/windows/apps/hh466130.aspx
            return {
                MOUSE:  0x00000004,
                TOUCH:  0x00000002,
                PEN:    0x00000003
            };
        } else { //other browsers don't support pointer events
            return {}; //return empty object
        }
    },

    /**
     * If called before init(), get best guess of input pointer type
     * using Modernizr test.
     * If called after init(), get current pointer in use.
     */
    getPointer: function() {
        // On iOS devices, always default to touch, as this.lastTouchType will intermittently return 'mouse' if
        // multiple touches are triggered in rapid succession in Safari on iOS
        if(Modernizr.ios) {
            return this.TOUCH_POINTER_TYPE;
        }

        if(this.lastTouchType) {
            return this.lastTouchType;
        }

        return Modernizr.touch ? this.TOUCH_POINTER_TYPE : this.MOUSE_POINTER_TYPE;
    },

    setPointerEventLock: function() {
        this.pointerEventLock = true;
    },
    clearPointerEventLock: function() {
        this.pointerEventLock = false;
    },
    setPointerEventLockTimeout: function() {
        var that = this;

        if(this.pointerTimeout) {
            clearTimeout(this.pointerTimeout);
        }

        this.setPointerEventLock();
        this.pointerTimeout = setTimeout(function() { that.clearPointerEventLock(); }, this.POINTER_EVENT_TIMEOUT_MS);
    },

    triggerMouseEvent: function(originalEvent) {
        if(this.lastTouchType == this.MOUSE_POINTER_TYPE) {
            return; //prevent duplicate events
        }

        this.lastTouchType = this.MOUSE_POINTER_TYPE;
        jQuery(window).trigger('mouse-detected', originalEvent);
    },
    triggerTouchEvent: function(originalEvent) {
        if(this.lastTouchType == this.TOUCH_POINTER_TYPE) {
            return; //prevent duplicate events
        }

        this.lastTouchType = this.TOUCH_POINTER_TYPE;
        jQuery(window).trigger('touch-detected', originalEvent);
    },

    initEnv: function() {
        if (window.navigator.pointerEnabled) {
            this.standardTouch = true;
            this.touchDetectionEvent = 'pointermove';
        } else if (window.navigator.msPointerEnabled) {
            this.standardTouch = true;
            this.touchDetectionEvent = 'MSPointerMove';
        } else {
            this.touchDetectionEvent = 'touchstart';
        }
    },

    wirePointerDetection: function() {
        var that = this;

        if(this.standardTouch) { //standard-based touch events. Wire only one event.
            //detect pointer event
            jQuery(window).on(this.touchDetectionEvent, function(e) {
                switch(e.originalEvent.pointerType) {
                    case that.getPointerEventsInputTypes().MOUSE:
                        that.triggerMouseEvent(e);
                        break;
                    case that.getPointerEventsInputTypes().TOUCH:
                    case that.getPointerEventsInputTypes().PEN:
                        // intentionally group pen and touch together
                        that.triggerTouchEvent(e);
                        break;
                }
            });
        } else { //non-standard touch events. Wire touch and mouse competing events.
            //detect first touch
            jQuery(window).on(this.touchDetectionEvent, function(e) {
                if(that.pointerEventLock) {
                    return;
                }

                that.setPointerEventLockTimeout();
                that.triggerTouchEvent(e);
            });

            //detect mouse usage
            jQuery(document).on('mouseover', function(e) {
                if(that.pointerEventLock) {
                    return;
                }

                that.setPointerEventLockTimeout();
                that.triggerMouseEvent(e);
            });
        }
    },

    init: function() {
        this.initEnv();
        this.wirePointerDetection();
    }
};

var ProductMediaManager = {
    swapImageDetail : function(base_image,zoom_image,thumb_image){
        if(jQuery('.col-main-details').hasClass('col-main-details-style_2')){
            ConfigurableMediaImages.checkImageLoaded(base_image,function(image){
                jQuery('.product-view .product-img-box-top .arw-fancybox').last().attr('href',image).find('img').attr('src',image);
            });
        }else{
            ConfigurableMediaImages.checkImageLoaded(thumb_image,function(image){
                jQuery('.product-view #arw-zoom img').attr('src',image);
            });
            ConfigurableMediaImages.checkImageLoaded(zoom_image,function(image){
                if(!jQuery('body').hasClass('arexworks-quickview-index')){
                    jQuery('.product-view #arw-zoom').data('zoom').destroy();
                    jQuery('.product-view #arw-zoom').attr('href',image);
                    jQuery('.product-view #arw-zoom').CloudZoom();
                }
            });
            if(jQuery('.zoom-fancybox-button').parent().length > 0){
                var flag = true;
                jQuery('.zoom-fancybox-button').each(function(){
                    if(jQuery(this).data('image') == thumb_image){
                        flag = false;
                    }
                })
                if(flag){
                    var $parent_popup = jQuery('.zoom-fancybox-button').parent();
                    var $popup_object = jQuery('<a/>');
                    $popup_object.addClass('zoom-fancybox-button')
                        .attr('rel','gallery')
                        .attr('href',base_image)
                        .data('image',thumb_image);
                    $parent_popup.append($popup_object);
                }
            }
        }
    },
    swapImageList : function(list_image , $product){
        ConfigurableMediaImages.checkImageLoaded(list_image,function(image){
            jQuery('img.product-collection-image',$product).attr('src',image);
        })
    },
    init: function() {
        jQuery(document).trigger('product-media-loaded', ProductMediaManager);
    }
};
jQuery(document).ready(function(){
    PointerManager.init();
    ProductMediaManager.init();
});