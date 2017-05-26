var arexworks;

function setupCustomMap(gmapID , zoom, latitude, longitude, mapIsNotActive ) {
    if (mapIsNotActive) {

        var styles = [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}];

        var lt, ld;

        var destination = new google.maps.LatLng(latitude, longitude);

        var options = {
            mapTypeControlOptions: {
                mapTypeIds: ['Styled']
            },
            center: destination,
            zoom: zoom,
            disableDefaultUI: true,
            scrollwheel: false,
            mapTypeId: 'Styled'
        };

        var div = document.getElementById(gmapID);


        var map = new google.maps.Map(div, options);


        var styledMapType = new google.maps.StyledMapType(styles, {
            name: 'Styled'
        });
        var infowindow = new google.maps.InfoWindow({
            content: jQuery('#'+gmapID+'_infowindow').html()
        });
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(latitude, longitude),
            map: map,
            icon: jQuery('#'+gmapID).data('icon'),
            infoWindow: infowindow
        });
        infowindow.open(map,marker);
        map.mapTypes.set('Styled', styledMapType);
        google.maps.event.addListener(infowindow, 'domready', function() {
            jQuery('.gm-style-iw').parent().addClass('arw-custom-infowindow');
            jQuery('.gm-style-iw').parent().parent().parent().prev().addClass('arw-custom-overlay-gmap');
        });
        mapIsNotActive = false;
    }
}
jQuery(document).ready(function(){
    try {
        jQuery('.google-map').each(function(){
            setupCustomMap(jQuery(this).attr('id'),jQuery(this).data('zoom'),jQuery(this).data('latitude'),jQuery(this).data('longitude'),true);
        });
    }catch (ex){ console.log(ex); }
});

//paralax position
jQuery(window).scroll(function() {
    var x = jQuery(this).scrollTop();
    jQuery('.parallax-box').each(function(){
        var top = jQuery(this).offset().top;
        var height = jQuery(this).outerHeight();
        var window_height = jQuery(window).height();
        var scroll_top = jQuery(this).scrollTop();
        //if(scroll_top > (top-window_height))

        jQuery(this).css('background-position', '40%' + parseInt(-(x-top) / 10) + 'px');

    });
});


//js benz
jQuery(document).ready(function() {
    if (jQuery('.header-container').hasClass('header_style_2')){
        jQuery('.header-container').find('.header-fullwidth').removeClass('container').css('margin','0 50px');
    }
    if (jQuery('.header-container').hasClass('header_style_5')){
        jQuery('.header-container').parent().addClass('nofooter-home6');
    }
    if (jQuery('.header-container').hasClass('header_style_3')){
        jQuery('.block_home_three_1').find('.btn-cart').html('Add to cart');
    }
    jQuery('.menu-colection').parent().parent().addClass('collection-megamenu');
    jQuery('.header-container.header_style_5').removeClass('header_fixed_menu');


    jQuery('.btn-menu').click(function () {
        jQuery(this).parent().find('.header_style_5').toggle();
    });
    jQuery('.benz-myaccount').find('ul.links').addClass('dropdown-account-benz');
    jQuery('.header-language').find('ul.links').addClass('dropdown-language-benz');
    jQuery('.current-benz').find('ul.links').addClass('dropdown-current-benz');

    jQuery('.down-account-benz').click(function () {
        jQuery(this).parent().find('.dropdown-account-benz').slideToggle('500');
    });
    jQuery('.down-language-benz').click(function () {
        jQuery(this).parent().find('.dropdown-language-benz').slideToggle('500');
    });
    jQuery('.down-current-benz').click(function () {
        jQuery(this).parent().find('.dropdown-current-benz').slideToggle('500');
    });

    jQuery('.btn-readmore-about-benz').click(function () {
        jQuery(this).parent().find('.about-benz-text1').toggle('slow');
    });


    jQuery(window).resize(function(){
        var w = jQuery(window).width();
        if(w > 320 && jQuery('.header_style_5').is(':hidden')) {
            jQuery('.header_style_5').removeAttr('style');
        }
    });
    jQuery(window).load(function(){
        $heightab = jQuery('.dynamic-height').height();
        jQuery('.img-about-us').height($heightab);
    });
    jQuery(window).resize(function(){
        $heightab = jQuery('.dynamic-height').height();
        jQuery('.img-about-us').height($heightab);
    });

    jQuery(window).load(function($){
        if(jQuery(window).width() > 1075){
            var $heiheader = jQuery('.header_style_5 #header'),
                heightheader = $heiheader.height(),
                heistyle5 = heightheader + 240 ;
            jQuery('.header_style_5').height(heistyle5);
        }
    });

    jQuery(".slide-menu").owlCarousel({
        items : 1, //10 items above 1000px browser width;
        itemsDesktop : [1024,1], //6 items between 1000px and 901px;
        itemsDesktopSmall : [900,1], // 3 items betweem 900px and 601px;
        itemsTablet: [600,1], //2 items between 600 and 0;
        itemsMobile : [320,1], //1 items between 600 and 0;
        navigation : true,
        navigationText : ["<i class=\"fa fa-angle-left\"></i>","<i class=\"fa fa-angle-right\"></i>"],
        pagination : false
    });

    window.onscroll = function() {myFunction()};
    function myFunction() {
        var local = jQuery('.our_skills');
        if (local.length){
            var x = 0,
                x = x + local.offset().top;
        }
        if (document.body.scrollTop > (x-300) || document.documentElement.scrollTop > (x-300)) {
            jQuery('.skillbar').each(function(){
                var percent = jQuery(this).data("percent");
                jQuery(this).find('.skillbar-bar').animate({
                    width:jQuery(this).attr('data-percent')
                },3000);
                jQuery(this).find('.percent').html(percent);
            });
        }
    }

    jQuery('.block-related').css('display','none');
    jQuery('.block-related').parent().find('.arw-title.text-uppercase').css('display','none');
    jQuery('.block-related').parent().find('.arw-sub-title').css('display','none');

    jQuery('.has-background-page-title').parent().find('.page-title').find('h1').addClass('color-white');

    jQuery('.btn-open-search').click(function(){
        jQuery(this).parent().find('.top-search-inner').slideToggle('500');
    });

});

jQuery('header').ready(function(){
    if (jQuery('.header-container').hasClass('header_style_3')){
        jQuery(this).find('.header-mini-search').addClass('style_search_2');
    }

    jQuery('.btn_menu_mobile').click(function(){
        jQuery('.btn_menu_mobile').parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('.arw-mobile-menu').css({'transform':'translate(0,0)'});
        jQuery('.btn_menu_mobile').parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('.page').css({'transform':'translate(300px,0)'});
    });

    jQuery('.arw-mobile-menu-header').click(function(){
        jQuery('.arw-mobile-menu-header').parent().parent().find('.arw-mobile-menu').css({'transform':'translate(-300px,0)'});
        jQuery('.arw-mobile-menu-header').parent().parent().find('.page').css({'transform':'translate(0,0)'});
    });
});
jQuery('.header-quick-access').ready(function(){

    jQuery('.btn-headertop').click(function(){
        jQuery(this).parent().parent().find('.top-link-benz').slideToggle('500');
    });

});


/*end*/
function equalheight(container){
    var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = new Array(),
        $el,
        topPosition = 0;
    container.each(function() {
        $el = jQuery(this);
        jQuery($el).height('auto');
        topPosition = $el.offset().top;
        if (currentRowStart != topPosition) {
            for (var currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }
            rowDivs.length = 0; // empty the array
            currentRowStart = topPosition;
            currentTallest = $el.height();
            rowDivs.push($el);
        } else {
            rowDivs.push($el);
            currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
        }
        for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
        }
    });
};
function equalheight2(container,class_name){
    var rowDivs = [],
        rowCount = container.find('li.item').first().children('.wrapper-item').length;
    for(var i = 0 ; i < rowCount ; i++){
        rowDivs.push(new Array());
    }
    container.find('li.item').each(function(){
        var i = 0;
        jQuery(this).children('.wrapper-item').each(function(){
            rowDivs[i].push(jQuery(this));
            i++;
        })
    });
    for(var i = 0 ; i < rowCount ; i++){
        var maxHeight = 0;
        for( var j = 0 ; j < rowDivs[i].length ; j++){
            rowDivs[i][j].find(class_name).height('auto');
            if(rowDivs[i][j].find(class_name).height() > maxHeight){
                maxHeight = rowDivs[i][j].find(class_name).height();
            }
        }
        for( var j = 0 ; j < rowDivs[i].length ; j++){
            rowDivs[i][j].find(class_name).height(maxHeight);
        }
    }
};
function arw_equal_height(container){
    equalheight(container);
    jQuery(window).resize(function(){
        equalheight(container);
    })
};
(function($) {
    'use strict';
    if(typeof arexworks === 'undefined'){
        arexworks = {}
    }
    var frontend = arexworks.Frontend = {
        init : function(){
            this.jQueryExtensions();
            this.another();
            this.equalHeight();
            this.selectBox();
            this.initTooltip();
            this.initOwlCarousel();
            this.toggleSidebarMenu();
            this.reviewFormClick();
            this.stickNavigation();
            this.scrollToTop();
            this.minusAndPlusQty();
            this.initToggleMenuTopLink();
            this.initToggleSidebar();
            this.countdown();
            this.initFancybox();
            this.initResponsiveTable();
        },
        jQueryExtensions : function(){
            $.fn.extend({
                isTouchDevice : function(){
                    return ('ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0);
                },
                isEmail : function(email){
                    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return re.test(email);
                },
                arwAccordionMenu: function(options) {
                    var defaults = {
                        accordion: 'true',
                        speed: 300,
                        closedSign: 'collapse',
                        closedSignText : '<i class="fa fa-plus-circle"></i>',
                        openedSign: 'expand',
                        openedSignText: '<i class="fa fa-minus-circle"></i>',
                        mouseType: 1
                    };
                    var opts = $.extend(defaults, options);
                    var $this = $(this);
                    $this.find("li").each(function() {
                        if($(this).find("ul").size() != 0){
                            $(this).find("a:first").after("<span class='"+ opts.closedSign +"'>"+ opts.closedSignText +"</span>");
                            if($(this).find("a:first").attr('href') == "#"){
                                $(this).find("a:first").click(function(){return false;});
                            }
                        }
                    });
                    $this.find("li.active").each(function() {
                        $(this).parents("ul").slideDown(opts.speed, opts.easing);
                        $(this).parents("ul").parent("li").find("a:first").next().html(opts.openedSignText).removeClass(opts.closedSign).addClass(opts.openedSign);
                        $(this).find("ul:first").slideDown(opts.speed, opts.easing);
                        $(this).find("a:first").next().html(opts.openedSignText).removeClass(opts.closedSign).addClass(opts.openedSign);
                    });
                    if(opts.mouseType==0){
                        $this.find("li span").click(function() {
                            if($(this).parent().find("ul").size() != 0){
                                if(opts.accordion){
                                    //Do nothing when the list is open
                                    if(!$(this).parent().find("ul").is(':visible')){
                                        var parents = $(this).parent().parents("ul"),
                                            visible = $this.find("ul:visible");
                                        visible.each(function(visibleIndex){
                                            var close = true;
                                            parents.each(function(parentIndex){
                                                if(parents[parentIndex] == visible[visibleIndex]){
                                                    close = false;
                                                    return false;
                                                }
                                            });
                                            if(close){
                                                if($(this).parent().find("ul") != visible[visibleIndex]){
                                                    $(visible[visibleIndex]).slideUp(opts.speed, function(){
                                                        $(this).parent("li").find("a:first").next().html(opts.closedSignText).removeClass(opts.openedSign).addClass(opts.closedSign);
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                                if($(this).parent().find("ul:first").is(":visible")){
                                    $(this).parent().find("ul:first").slideUp(opts.speed, opts.easing, function(){
                                        $(this).parent("li").find("a:first").next().delay(opts.speed+1000).html(opts.closedSignText).removeClass(opts.openedSign).addClass(opts.closedSign);
                                    });
                                }else{
                                    $(this).parent().find("ul:first").slideDown(opts.speed, opts.easing, function(){
                                        $(this).parent("li").find("a:first").next().delay(opts.speed+1000).html(opts.openedSignText).removeClass(opts.closedSign).addClass(opts.openedSign);
                                    });
                                }
                            }
                        });
                    }
                    if(opts.mouseType>0){
                        $this.find("li a").mouseenter(function() {
                            if($(this).parent().find("ul").size() != 0){
                                if(opts.accordion){
                                    if(!$(this).parent().find("ul").is(':visible')){
                                        var parents = $(this).parent().parents("ul"),
                                            visible = $this.find("ul:visible");
                                        visible.each(function(visibleIndex){
                                            var close = true;
                                            parents.each(function(parentIndex){
                                                if(parents[parentIndex] == visible[visibleIndex]){
                                                    close = false;
                                                    return false;
                                                }
                                            });
                                            if(close){
                                                if($(this).parent().find("ul") != visible[visibleIndex]){
                                                    $(visible[visibleIndex]).slideUp(opts.speed, function(){
                                                        $(this).parent("li").find("a:first").next().html(opts.closedSignText).addClass(opts.closedSign);
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                                if($(this).parent().find("ul:first").is(":visible")){
                                    $(this).parent().find("ul:first").slideUp(opts.speed, function(){
                                        $(this).parent("li").find("a:first").next().delay(opts.speed+1000).html(opts.closedSignText).removeClass(opts.openedSign).addClass(opts.closedSign);
                                    });
                                }else{
                                    $(this).parent().find("ul:first").slideDown(opts.speed, function(){
                                        $(this).parent("li").find("a:first").next().delay(opts.speed+1000).html(opts.openedSignText).removeClass(opts.closedSign).addClass(opts.openedSign);
                                    });
                                }
                            }
                        });
                    }
                },
                megamenu : function(options){
                    options = $.extend({
                        animation: "show",
                        mm_timeout: 0
                    }, options);
                    var megamenu_object = this;
                    megamenu_object.find("li.parent").each(function(){
                        var cat_icon = '<i class="item-has-sub fa fa-caret-right"></i>';
                        if($(this).hasClass('level0')){
                            cat_icon = '<i class="item-has-sub fa fa-caret-down"></i>';
                        }
                        $(this).children('a').append(cat_icon);
                        var mm_item = $(this).children('div');
                        mm_item.hide();
                        mm_item.wrapInner('<div class="list_item_dropdown clearfix"></div>');
                        var timer = 0;
                        $(this).bind('mouseenter', function(e){
                            $(this).children('a').addClass('arw-hover');
                            var mm_item_obj = $(this).children('div');
                            clearTimeout(timer);
                            var check_menu = $(this).find('div.list_item_dropdown');
                            if(mm_item.length >0 && check_menu.length >0){
                                if(check_menu.html().length>1){
                                    timer = setTimeout(function(){
                                        switch(options.animation) {
                                            case "show":
                                                mm_item_obj.show().addClass("shown-sub");
                                                break;
                                            case "slide":
                                                mm_item_obj.height("auto");
                                                mm_item_obj.stop().slideDown('fast', function(){
                                                    mm_item_obj.css("overflow","inherit");
                                                }).addClass("shown-sub");
                                                break;
                                            case "fade":
                                                mm_item_obj.stop().fadeTo('fast', 1).addClass("shown-sub");
                                                break;
                                        }
                                        setTimeout(function(){
                                            arw_equal_height(mm_item.find('.products-grid li.item .product-info'));
                                        },200)
                                    }, options.mm_timeout);
                                }
                            }
                        });
                        $(this).bind('mouseleave', function(e){
                            var mm_item_obj = $(this).children('div');
                            $(this).children('a').removeClass('arw-hover');
                            clearTimeout(timer);
                            switch(options.animation) {
                                case "show":
                                    mm_item_obj.hide().removeClass('shown-sub');
                                    break;
                                case "slide":
                                    mm_item_obj.stop().removeClass('shown-sub').slideUp( 'fast',  function() {});
                                    break;
                                case "fade":
                                    mm_item_obj.stop().removeClass('shown-sub').fadeOut( 'fast', function() {});
                                    break;
                            }
                        });
                    });
                    this.show();
                }
            });
        },
        equalHeight : function(){
            setTimeout(function(){
                arw_equal_height($('.element_equal_height'));
            },1000);
        },
        selectBox : function(){
            if(typeof $.fn.selectpicker === 'function'){
                var selectpickerEl = [
                    '.header-link select',
                    '.sort-by select',
                    '.limiter select',
                    '.search-select-cat'
                ];
                selectpickerEl.each(function(index){
                    var obj = $(index);
                    if(!obj.hasClass('hasInstall')){
                        obj.selectpicker();
                        obj.addClass('hasInstall');
                    }
                })
            }
        },
        initTooltip : function(){
            $("[data-toggle=tooltip]").tooltip();
        },
        minusAndPlusQty : function(){
            var minus = $('.arw-box-quantity .wrap').find('.qty-minus'),
                plus = $('.arw-box-quantity .wrap').find('.qty-plus');
            if(minus.length > 0){
                minus.on('click',function(){
                    var value = parseInt($(this).parent().children('.qty').val());
                    if(value > 0){
                        value = value - 1;
                    }
                    $(this).parent().children('.qty').val(value).trigger('onchange');
                })
            }
            if(plus.length > 0){
                plus.on('click',function(){
                    var value = parseInt($(this).parent().children('.qty').val());
                    value = value + 1;
                    $(this).parent().children('.qty').val(value).trigger('onchange');
                })
            }
        },
        initOwlCarousel : function(){
            function changePositionControl($owl){
                if($owl.hasClass('products-grid') && ($owl.hasClass('arw-control-style-1') || $owl.closest('.arw-control-style-1').length > 0 || $owl.hasClass('.carousel_style_1') || $owl.closest('.carousel_style_1').length > 0)){
                    var _item_height = $owl.find('.outer-image').eq(0).innerHeight(),
                        _margin_top = Math.round((_item_height - 40) / 2) + 1;
                    $owl.find('.owl-nav').css('top',_margin_top);
                }
            }
            if(typeof $.fn.owlCarousel !== 'undefined'){

                $('.arw-carousel').each(function(){
                    var responsive = '';
                    if($(this).data('responsive')){
                        responsive = '"0":{"items":1}';
                        var str = $(this).data('responsive');
                        var tmp2 = str.split(",");
                        if(tmp2 != null && tmp2.length > 0){
                            for(var i = 0 ; i < tmp2.length ; i++){
                                var tmp3 = tmp2[i].split(":");
                                if(tmp3.length > 1){
                                    responsive += ',"' + tmp3[0] + '":';
                                    responsive += '{"items":'+tmp3[1]+'}';
                                }else{
                                    responsive = '';
                                }
                            }
                        }
                        responsive = '{' + responsive + '}';
                    }
                    var opts = {
                        autoplay : $(this).data('autoplay') ? Boolean($(this).data('autoplay')) : false,
                        nav:$(this).data('nav') ? Boolean($(this).data('nav')) : false,
                        loop:$(this).data('loop') ? Boolean($(this).data('loop')) : false,
                        dots:$(this).data('dot') ? Boolean($(this).data('dot')) : false,
                        items:$(this).data('item') ? parseInt($(this).data('item')) : 1,
                        margin:$(this).data('margin') ? parseInt($(this).data('margin')) : 0,
                        themeClass:$(this).data('theme_class') ? $(this).data('theme_class') : 'owl-theme'
                    };
                    if(responsive != ''){
                        opts.responsive = JSON.parse(responsive)
                    }
                    if($(this).data('autoplaytimeout')){
                        opts.autoplayTimeout = parseInt($(this).data('autoplaytimeout'));
                    }
                    if($(this).data('smartspeed')){
                        opts.smartSpeed = parseInt($(this).data('smartspeed'));
                    }
                    if($(this).data('autoplayspeed')){
                        opts.autoplaySpeed = parseInt($(this).data('autoplayspeed'));
                    }
                    if($(this).data('navspeed')){
                        opts.navSpeed = parseInt($(this).data('navspeed'));
                    }
                    if($(this).data('dotsspeed')){
                        opts.dotsSpeed = parseInt($(this).data('dotsspeed'));
                    }
                    var _owl = $(this);
                    _owl.owlCarousel(opts);
                    if($(this).data('btn_next')){
                        $($(this).data('btn_next')).on('click',function(){
                            _owl.trigger('next.owl.carousel');
                        })
                    }
                    if($(this).data('btn_prev')){
                        $($(this).data('btn_prev')).on('click',function(){
                            _owl.trigger('prev.owl.carousel');
                        })
                    }
                });

                $('.arw-carousel-for-product').each(function(){
                    var responsive = '' ,
                        _owl = $(this).find('.products-carousel');
                    if(_owl.length > 0){
                        if($(this).data('responsive')){
                            responsive = '"0":{"items":1}';
                            var str = $(this).data('responsive');
                            var tmp2 = str.split(",");
                            if(tmp2 != null && tmp2.length > 0){
                                for(var i = 0 ; i < tmp2.length ; i++){
                                    var tmp3 = tmp2[i].split(":");
                                    if(tmp3.length > 1){
                                        responsive += ',"' + tmp3[0] + '":';
                                        responsive += '{"items":'+tmp3[1]+'}';
                                    }else{
                                        responsive = '';
                                    }
                                }
                            }
                            responsive = '{' + responsive + '}';
                        }
                        var opts = {
                            autoplay : $(this).data('autoplay') ? Boolean($(this).data('autoplay')) : false,
                            nav:$(this).data('nav') ? Boolean($(this).data('nav')) : false,
                            loop:$(this).data('loop') ? Boolean($(this).data('loop')) : true,
                            dots:$(this).data('dot') ? Boolean($(this).data('dot')) : false,
                            items:$(this).data('item') ? parseInt($(this).data('item')) : 1,
                            margin:$(this).data('margin') ? parseInt($(this).data('margin')) : 0,
                            themeClass:$(this).data('theme_class') ? $(this).data('theme_class') : 'owl-theme'
                        };
                        if(responsive != ''){
                            opts.responsive = JSON.parse(responsive)
                        }
                        if($(this).data('autoplaytimeout')){
                            opts.autoplayTimeout = parseInt($(this).data('autoplaytimeout'));
                        }
                        if($(this).data('smartspeed')){
                            opts.smartSpeed = parseInt($(this).data('smartspeed'));
                        }
                        if($(this).data('autoplayspeed')){
                            opts.autoplaySpeed = parseInt($(this).data('autoplayspeed'));
                        }
                        if($(this).data('navspeed')){
                            opts.navSpeed = parseInt($(this).data('navspeed'));
                        }
                        if($(this).data('dotsspeed')){
                            opts.dotsSpeed = parseInt($(this).data('dotsspeed'));
                        }
                        _owl.owlCarousel(opts);

                        if($(this).data('btn_next')){
                            $($(this).data('btn_next')).on('click',function(){
                                _owl.trigger('next.owl.carousel');
                            })
                        }
                        if($(this).data('btn_prev')){
                            $($(this).data('btn_prev')).on('click',function(){
                                _owl.trigger('prev.owl.carousel');
                            })
                        }
                    }
                });
                setTimeout(function(){
                    $('.owl-carousel').each(function(){
                        var $this = $(this);
                        changePositionControl($this);
                    })
                },1200);
                $(document).on('refresh.owl.carousel','.owl-carousel',function(e){
                    var $this = $(this);
                    setTimeout(function(){
                        changePositionControl($this);
                    },1200);
                });
                $(window).load(function(){
                    $('.owl-carousel').each(function(){
                        var $this = $(this);
                        setTimeout(function(){
                            changePositionControl($this);
                        },1200);
                    })
                })
            }
        },
        initOwlGalleryImage : function($owl){
            $owl.each(function(i, el)
            {
                var $this = $(el),
                    $images = $this.find('.thumbnail-item');
                if($images.length > 1)
                {
                    $this.append( '<div class="catalog-owl-nav"><div class="catalog-owl-prev">prev</div><div class="catalog-owl-next">next</div></div>' );
                    var	$nextprev = $this.find('.catalog-owl-prev, .catalog-owl-next');
                    $nextprev.on('click', function(ev)
                    {
                        ev.preventDefault();
                        var dir = $(this).hasClass('catalog-owl-prev') ? -1 : 1,
                            $curr = $images.filter(':not(.hidden-slowly)'),
                            $next = $curr.next();
                        if(dir == 1)
                        {
                            if($next.length == 0)
                                $next = $images.first();
                        }
                        else
                        {
                            $next = $curr.prev();
                            if($next.length == 0)
                                $next = $images.last();
                        }
                        $next.addClass('enter-in notrans ' + (dir == -1 ? 'ei-left' : '')).removeClass('hidden hidden-slowly hs-left hs-right');
                        $curr.addClass('hidden-slowly ' + (dir == -1 ? 'hs-left' : ''));
                        setTimeout(function(){ $next.removeClass('enter-in notrans ei-left'); }, 0);
                    });
                }
            });
        },
        toggleSidebarMenu : function(){
            var menu = $('.arw-mobile-menu'),
                body = $('body'),
                container = $('.wrapper > .page'),
                siteOverlay = $('.site-overlay,.arw-mobile-menu-header'),
                menuActiveClass = "arw-menu-open",
                menuBtn = $('.arw-btn-menu-mobile'),
                menuSpeed = 200,
                menuWidth = menu.width() + "px",
                menuLayout= menuBtn.data('style') ? menuBtn.data('style') : 'accordion';

            function toggleMenu(){
                body.toggleClass(menuActiveClass);
            }

            function openMenuFallback(){
                body.toggleClass(menuActiveClass);
                menu.animate({left: "0px"}, menuSpeed);
                container.animate({left: menuWidth}, menuSpeed);
            }

            function closeMenuFallback(){
                body.removeClass(menuActiveClass);
                menu.animate({left: "-" + menuWidth}, menuSpeed);
                container.animate({left: "0px"}, menuSpeed);
            }

            var cssTransforms3d = (function csstransforms3d(){
                var el = document.createElement('p'),
                    supported = false,
                    transforms = {
                        'webkitTransform':'-webkit-transform',
                        'OTransform':'-o-transform',
                        'msTransform':'-ms-transform',
                        'MozTransform':'-moz-transform',
                        'transform':'transform'
                    };

                // Add it to the body to get the computed style
                document.body.insertBefore(el, null);

                for(var t in transforms){
                    if( el.style[t] !== undefined ){
                        el.style[t] = 'translate3d(1px,1px,1px)';
                        supported = window.getComputedStyle(el).getPropertyValue(transforms[t]);
                    }
                }

                document.body.removeChild(el);

                return (supported !== undefined && supported.length > 0 && supported !== "none");
            })();
            if(menuLayout == 'accordion'){
                menuBtn.click(function(){
                    $(this).closest('.nav-primary-container').find('.arw-mobile-menu').animate({
                        height : 'toggle'
                    })
                })
            }else{
                if(cssTransforms3d){
                    menuBtn.click(function() {
                        toggleMenu();
                    });
                    siteOverlay.click(function(){
                        toggleMenu();
                    });
                }else{
                    menu.css({left: "-" + menuWidth});
                    container.css({"overflow-x": "hidden"});
                    var state = true;
                    menuBtn.click(function() {
                        if (state) {
                            openMenuFallback();
                            state = false;
                        } else {
                            closeMenuFallback();
                            state = true;
                        }
                    });
                    siteOverlay.click(function(){
                        if (state) {
                            openMenuFallback();
                            state = false;
                        } else {
                            closeMenuFallback();
                            state = true;
                        }
                    });
                }
            }
        },
        reviewFormClick : function(){
            $('.product-view .rating-links a,.product-view .no-rating a').on('click',function(e){
                e.preventDefault();
                $('#product_tabs_tabreviews').trigger('click');
                var top = $('#product_tabs_tabreviews_contents').offset().top;
                $(window).scrollTop(top);
            })
        },
        stickNavigation : function(){
            if($('.header_fixed_menu').length > 0){
                var $header = $('.header_fixed_menu'),
                    $nav = $('.nav-primary-container'),
                    sticky_navigation_offset_top = $nav.offset().top + $nav.innerHeight() + 50;
                if($header.hasClass('header_style_2')){
                    sticky_navigation_offset_top = $header.innerHeight();
                }
                var sticky_navigation = function (){
                    var scroll_top = $(window).scrollTop();
                    if (scroll_top > sticky_navigation_offset_top && $(window).width() > 768) {
                        $header.addClass('active-sticky');
                        if($header.hasClass('header_style_1')){
                            $header.css('margin-top',$nav.innerHeight());
                        }
                    } else {
                        $header.removeClass('active-sticky').removeAttr('style');
                    }
                };
                sticky_navigation();
                $(window)
                    .scroll(function() {
                        sticky_navigation();
                    })
                    .resize(function(){
                        sticky_navigation();
                    })
            }
        },
        scrollToTop : function(){
            var html = '<div id="btn_control_totop"><a href="#" class="btn2"><i class="fa fa-long-arrow-up"></i></a></div>';
            $('body').append(html);
            var scroll_top = function(){
                if($(window).scrollTop() > $(window).height()){
                    $('#btn_control_totop > a').fadeIn();
                }else{
                    $('#btn_control_totop > a').fadeOut();
                }
            }
            scroll_top();
            $(window).scroll(function(){
                scroll_top();
            });
            $('#btn_control_totop > a').on('click',function(e){
                e.preventDefault();
                $('body,html').animate({
                    scrollTop:0
                },800);
            })
        },
        initToggleMenuTopLink : function(){

            $('.search-select-cat .dropdown-toggle').on('click',function(){
                $(this).closest('.arw-toggle-container').addClass('active');
            });
            $('body').on('click',function(e){
                if($(e.target).closest('.arw-toggle-container').length == 0){
                    $('.arw-toggle-container').removeClass('active');
                }
            })
        },
        initToggleSidebar : function(){
            $('.main-container').append('<div class="arw-overlay-close"></div>');
            $('.arw-overlay-close').on('click',function(){
                $('body').removeClass('active-sidebar-left').removeClass('active-sidebar-right');
            });
            $('.arw-toggle-sidebar').on('click',function(){
                $('body').addClass($(this).data('class'));
            });
        },
        gridEqualHeight : function(){
            setTimeout(function(){
                arw_equal_height($('.products-grid:not(.products-slide) li.item .product-name'));
            },1000);
        },
        countdown : function(){
            if(typeof $.fn.countdown === "function"){
                $('.arw-countdown-for-product').each(function(){
                    if($(this).closest('.product-tab-list-mini').length > 0){
                        $(this).remove();
                    }
                })
                $('.arw-countdown-for-product').countdown({
                    dataAttr: 'cdate',
                    spaceCharacter:'',
                    template : '<div class="background-primary"><div class="c-item">%d</div><div class="c-item">%h</div><div class="c-item">%i</div><div class="c-item">%s</div></div>',
                    daysLeft : '',
                    hrsLeft : '',
                    minsLeft : '',
                    secLeft : ''
                });
            }
        },
        initFancybox : function(){
            if(typeof $.fn.fancybox === "function"){
                $('.arw-fancybox').fancybox();
            }
        },
        initResponsiveTable : function(){
            $("table:not(.cart-table,#wishlist-table,#multiship-addresses-table)").each(function(){
                var _this = $(this);
                if(_this.children('thead').length > 0){
                    _this.addClass('arw-responsive-table');
                    var headertext = [];
                    var headers = _this.children('thead').find('th');
                    var table_tr = _this.children('tbody').children('tr');
                    headers.each(function(){
                        headertext.push($(this).text());
                    });
                    table_tr.each(function(){
                        var i = 0;
                        $(this).children('td').each(function(){
                            $(this).attr('data-th',headertext[i]);
                            i++;
                        })
                    })
                }
            })
        },
        another : function(){
            $('ul#checkout-progress-state li').each(function(){
                if(!$(this).hasClass('active')){
                    $(this).prepend('<i class="fa fa-check"></i>');
                }
            })
            if($('.toolbar-bottom .pager-select .pages').length == 0){
                $('.toolbar-bottom').removeClass('toolbar-bottom');
            }

            var full_screen = function(){
                $('.fluid-width').each(function(){
                    $(this).css({
                        marginLeft : 'auto',
                        marginRight : 'auto'
                    });
                    var width_w = $(window).width(),
                        width_e = $(this).innerWidth();
                    if(width_w > width_e){
                        $(this).css({
                            marginLeft : -(width_w - width_e) / 2,
                            marginRight : -(width_w - width_e) / 2
                        })
                    }
                });
                $('.fluid-content-width').each(function(){
                    $(this).css({
                        marginLeft : 'auto',
                        marginRight : 'auto',
                        paddingLeft : 'auto',
                        paddingRight : 'auto'
                    });
                    var width_w = $(window).width(),
                        width_e = $(this).innerWidth();
                    if(width_w > width_e){
                        $(this).css({
                            marginLeft : -(width_w - width_e) / 2,
                            marginRight : -(width_w - width_e) / 2,
                            paddingLeft : (width_w - width_e) / 2,
                            paddingRight : (width_w - width_e) / 2
                        })
                    }
                })
            }
            full_screen();
            $(window).resize(function(){
                full_screen();
            });
            var page_title = function(){
                if($('.col-main .page-title').length > 0){
                    var $container = $('</div>');
                    $('.col-main .page-title:not(.feature-brand-title)').wrapInner('<div class="container"></div>').prependTo($('.arw-page-title').addClass('has-page-title'));
                }
            }
            page_title();

            $('body').on('arw.resizeVideo',function(){
                if($('.video-wrap').length > 0){
                    $('.video-wrap').each(function(){
                        var $video = $(this).find('video');
                        if($video.length > 0){
                            var wap_height = $(this).data('video-height') ? $(this).data('video-height') : $(this).innerHeight;
                            var position = $(this).data('video-position') ? $(this).data('video-position') : 'top';
                            $video.height('auto');
                            var video_height = $(this).find('video').height();
                            $(this).css('height',wap_height);
                            if(video_height > wap_height){
                                switch (position){
                                    case 'bottom':
                                        $video.css({'top':'auto','bottom':'0'});
                                        break;
                                    case 'center':
                                        var top = (video_height - wap_height) / 2;
                                        $video.css({'top':-top,'bottom':'auto'})
                                        break;
                                    default :
                                        $video.css({'top':'0','bottom':'auto'});
                                }
                            }else{
                                $video.height(wap_height);
                                $video.css({'top':'0','bottom':'auto'});
                            }
                        }
                    })
                }
            });
            $('body').trigger('arw.resizeVideo');
            $(window).resize(function(){
                $('body').trigger('arw.resizeVideo');
            });
            // auto find dots in parallax slider;
            var auto_find_dots = '';
            $('.arw-parallax-slider .parallax-content-slider').each(function(index){
                index++;
                if(index < 10){
                    index = '0' + index;
                }
                var section_id = '#' + $(this).attr('id'),
                    content = '<a href="'+section_id+'" data-anchor-target="'+section_id+'" data-center-top="@class:active;" data-center-bottom="@class:active" data-edge-strategy="reset"><span>'+index+'</span></a>';

                auto_find_dots += content;
            });
            if(auto_find_dots){
                auto_find_dots = '<div class="parallax-dots">'+auto_find_dots+'</div>';
                $('body .wrapper .page').append(auto_find_dots);
            }
            jQuery(document)
                .on('click','.slider-tab-toggle',function(e){
                    if(jQuery(this).next('.slider-tab').find('li.one_tab').length == 0){
                        jQuery(this).toggleClass('open-tab');
                    }
                })
                .on('click','.slider-tab li',function(e){
                    jQuery(this).closest('.arw_tab_slider').find('.slider-tab-toggle').removeClass('open-tab').html(jQuery(this).html());
                });
            jQuery('.slider-tab').each(function(){
                jQuery(this).find('li:first').trigger('click');
                if(jQuery(this).find('li.one_tab').length > 0){
                    jQuery(this).prev('.slider-tab-toggle').addClass('no-icon');
                }
            })
        },
        playVideo : function(video_id){
            var video = document.getElementById(video_id),
                wrap = $(video).closest('.video-wrap'),
                play = wrap.find('.btn-video-play'),
                overlay = wrap.find('.video-overlay');
            if (video.paused) {
                video.play();
                overlay.hide();
                play.find('i').removeAttr('class').addClass('icon-pause2');
            } else {
                video.pause();
                overlay.show();
                play.find('i').removeAttr('class').addClass('icon-play2');
            }
        },
        pauseVideo : function(video_id){
            arexworks.Frontend.playVideo(video_id);
        },
        replayVideo : function(video_id){
            document.getElementById(video_id).load();
            arexworks.Frontend.playVideo(video_id);
        },
        volumeVideo : function(video_id){
            var video = document.getElementById(video_id);
            var wrap = $(video).closest('.video-wrap');
            if(video.volume > 0){
                wrap.find('.btn-video-volume').addClass('btn-video-volume-off');
                video.volume = 0;
            }else{
                wrap.find('.btn-video-volume').removeClass('btn-video-volume-off');
                video.volume = 1;
            }
        },

        fixSlider : function(changeClass){
            var $logo    = $('.header-logo .logo img'),
                logo_src = $logo.attr('src'),
                logo_tmp = $logo.data('src-tmp');

            $logo.attr('data-src-tmp2',logo_src);

            function fixslider (){
                var $height_header = $('.header-container').innerHeight();

                if($(window).width() > 768){
                    var image = jQuery('<img />');
                    image.attr('src', logo_tmp);
                    try{
                        imagesLoaded(image,function(){
                            $logo.attr('src',logo_tmp);
                        });
                    }catch (ex){ console.log(ex);}
                    if(changeClass){
                        $('body').addClass('layout-header-special');
                    }
                    setTimeout(function(){
                        if($('.header-container').hasClass('active-sticky')){
                            $height_header = $height_header + $('.header-container .nav-primary-container').innerHeight();
                        }
                        $('.arw_layout_global_top').css({
                            'top':-$height_header,
                            'margin-bottom':-$height_header
                        });
                    },200);

                }else{
                    $logo.attr('src',$logo.attr('data-src-tmp2'));
                    $('body').removeClass('layout-header-special');
                    $('.arw_layout_global_top').removeAttr('style');
                }

            }
            fixslider();
            $(window).resize(function(){
                fixslider();
            })
        }
    }
    var extension = arexworks.Extension = {
        init : function(){
            this.quickview();
        },
        quickview : function(){
            if(typeof $.fn.fancybox === "function"){
                $(".link-quickview")
                    .fancybox({
                        type:'iframe',
                        autoSize:false,
                        width:'80%',
                        height:'80%',
                        title : '',
                        iframe: {
                            scrolling : 'auto',
                            preload   : true
                        }
                    });
                $('.arexworks-quickview-index a.link-wishlist').removeAttr('onclick');
                $(document).on('click','.arexworks-quickview-index a[href]',function(e){
                    var href = $(this).attr('href');

                    if($(this).hasClass('link-wishlist') || /checkout\/cart/.test(href)){
                        e.preventDefault();
                        parent.jQuery.fancybox.close();
                        parent.location.href = href;
                    }
                    if(href != '#' && $(this).closest('.product-img-box').length == 0 && $(this).closest('.product-options') == 0){
                        e.preventDefault();
                        parent.jQuery.fancybox.close();
                        parent.location.href = href;
                    }
                });
            }
            if(typeof $.fn.owlCarousel === 'function'){
                $('.arexworks-quickview-index .arw-carousel').owlCarousel({
                    dots : true,
                    nav : false,
                    autoplay : true,
                    items : 1
                })
            }
        }
    }
    $(function() {
        arexworks.Frontend.init();
        arexworks.Extension.init();
        if($.fn.isTouchDevice()){
            var a_element_need_hack_mobile = '.megamenu .parent > a,.outer-image a';
            $(a_element_need_hack_mobile).on('click',function(e){
                if(!$(this).hasClass('click-go-go')){
                    $(this).addClass('click-go-go');
                    e.preventDefault();
                }
            });
        }
        setTimeout(function(){
            var arw_skrollr = skrollr.init({
                mobileCheck: function() {
                    return false;
                }
            });
            skrollr.menu.init(arw_skrollr, {
                animate: true,
                easing: 'sqrt',
                scale: 2,
                updateUrl: false,
                duration: function(currentTop, targetTop) {
                    return 500;
                }
            });
        },300)
    });
    $(window).load(function(){
        arexworks.Frontend.gridEqualHeight();
    })
})(jQuery);


// override class ARWFilter
if(typeof ARWFilter != 'undefined'){
    var oldARWFilter = ARWFilter;
    ARWFilter = function () {
        oldARWFilter.apply(this, arguments);
        if (this.config.enable){
            document.observe('dom:loaded', function(){
                jQuery('[onchange^="setLocation"]').each(function(){
                    var onchange = jQuery(this).attr('onchange');
                    onchange = onchange.replace(/setLocation/g,'arwFilter.setAjaxLocation');
                    jQuery(this).attr('onchange',onchange);
                })
            }.bind(this));
        }
        this.sendRequest = function(url, success, error){
            if (this.config.enable){
                if (this.config.bar) NProgress.start();
                new Ajax.Request(url, {
                    parameters: this.getParams(),
                    onSuccess: function(transport){
                        try{
                            var response = transport.responseText.evalJSON(),
                                main = response.main ? response.main.replace(/setLocation/g, this.name+'.setAjaxLocation') : null,
                                layer = response.layer || null;

                            if (main && this.container) this.container.update(main);
                            if (layer && this.layer) this.layer.replace(layer);
                            setTimeout(function(){
                                this.collect();
                            }.bind(this));
                            if (success) success(transport);
                        }catch(e){
                            console.log(e.message);
                        }
                    }.bind(this),
                    onFailure: function(transport){
                        if (error) error(transport);
                    },
                    onComplete: function(){
                        if (this.config.bar) NProgress.done();
                        ajaxCartShoppCartLoad('.btn-cart');
                        ajaxCartShoppCartLoad('.link-cart');
                        jQuery(document).trigger('configurable-media-images-init');
                        arexworks.Frontend.initTooltip();
                        arexworks.Frontend.selectBox();
                        arexworks.Frontend.gridEqualHeight();
                        arexworks.Frontend.countdown();
                    }.bind(this)
                });
            }else{
                setLocation(url);
            }
        }
    };
    ARWFilter.prototype = oldARWFilter.prototype;
} ;
