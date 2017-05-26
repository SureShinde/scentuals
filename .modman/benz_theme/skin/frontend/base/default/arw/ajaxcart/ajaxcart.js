ajaxCart = Class.create();
ajaxCart.prototype = 
{
    options : null,  
    miniCartClass : '.mini-cart-header',
    url : null,
    updateUrl : null,
    isProductView : 0,
    productId : 0,

    initialize : function(options) {
        this.url = options['send_url'];
        this.updateUrl = options['update_url'];
        this.options = options;
        this.productId = options['product_id'];
        this.isProductView = options['is_product_view'];
    },

    sendAjax : function(idProduct, param, amEvent, element) {
        if(idProduct) {

            postData = 'product_id=' + idProduct;
            postData = this.addProductParam(postData,idProduct);
            if('' == postData)
                return true;
            new Ajax.Request(this.url, {
                method: 'post',
                postBody : postData,
                onCreate: function()
                {
                    this.disableClick();
                    jQuery(".loader").show();

                }.bind(this),
                onComplete: function()
                {
                    this.enableClick();
                    if($('arw_quickview_popup')!= undefined){
                        jQuery('.fancybox-overlay').trigger('click');
                    }
                    jQuery(".loader").hide();
                }.bind(this),
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.error) {
                            alert(response.error);
                        }
                        else{
                            if(response.redirect) {
                                //if IE7
                                if (document.all && !document.querySelector) {
                                    amEvent = amEvent.substring(21, amEvent.length-2)
                                    eval(amEvent);
                                }
                                else{
                                    eval(amEvent);
                                }
                                return true;
                            }
                            this.showPopup(response.dataOption,response.add_to_cart,response.action);
                            var maxHeight = parseInt($$('html')[0].getHeight()/4);
                            var height = document.getElementById('toPopup').getHeight();
                            if(!(height <= maxHeight)) {
                                $('toPopup').setStyle({
                                    overflowY : 'scroll',
                                    maxHeight : maxHeight + 'px'
                                });
                            }
                            if(response.add_to_cart === '1'){
                                parent.ajaxCartObj.updateMinicart();
                                parent.ajaxCartObj.updateCount(response.count)
                                this.updateSidebarCart();
                                this.updateShoppingCart();
                                this.updateMinicart();
                                this.updateCount(response.count);

                            }
                        }
                    }
                }.bind(this),
                onFailure: function()
                {
                    this.hideAnimation();
                    eval(amEvent);
                }.bind(this)
            });
        }
    },
    disableClick:function(){
        $$('.btn-cart').each(function(btn){
            btn.setAttribute('disabled', 'disabled');
        });
    },
    enableClick:function(){
        $$('.btn-cart').each(function(btn){
            btn.removeAttribute('disabled');
        });
    },
	addProductParam: function(postData,idProduct) {
	 var form="";
	 if(this.isProductView){
		if(idProduct==this.productId){
				form = $('product_addtocart_form');
			}else{
				if($$('#arw_quickview_popup #product_addtocart_form_quickview')[0]){
					form = $$('#arw_quickview_popup #product_addtocart_form_quickview')[0];
				}
			}
		}
	if(idProduct!=this.productId){
		 if($$('#toPopup #product_addtocart_form')[0]){
			form = $$('#toPopup #product_addtocart_form')[0];
		}
		if($$('#arw_quickview_popup #product_addtocart_form_quickview')[0]){
			form = $$('#arw_quickview_popup #product_addtocart_form_quickview')[0];
		}		
	}
        if(form) {
            var validator = new Validation(form);
            if (validator.validate()) {
				postData += "&" + jQuery(form).serialize()
            }
            else{
                 return '';                
            }
        }
        postData += '&IsProductView=' + this.isProductView;
        return postData;
    },
  /*   addProductParam: function(postData,idProduct) {
        var form="";
        if(this.isProductView){
            if(idProduct==this.productId){
                form = $('product_addtocart_form');
            }
        }
        if(idProduct!=this.productId){
            if($$('#toPopup #product_addtocart_form')[0]){
                form = $$('#toPopup #product_addtocart_form')[0];
            }
        }
        if(form) {
            var validator = new Validation(form);
            if (validator.validate()) {
                postData += "&" + jQuery(form).serialize()
            }
            else{
                return '';
            }
        }
        postData += '&IsProductView=' + this.isProductView;
        return postData;
    }, */
//	 addProductParam: function(postData) {
//        var form = $('product_addtocart_form');
//	 if($$('#toPopup #product_addtocart_form')[0]){
//		form = $$('#toPopup #product_addtocart_form')[0];
//	}
//        if(form) {
//            var validator = new Validation(form);
//            if (validator.validate()) {
//                jQuery("#toPopup").fadeOut("normal");
//                jQuery("#backgroundPopup").fadeOut("normal");
//				postData += "&" + jQuery(form).serialize();
//            }
//            else{
//                 return '';
//            }
//        }
//        postData += '&IsProductView=' + this.isProductView;
//        return postData;
//    },
	
	createPopup : function(){
		$$('body')[0].insert("<div id='toPopup'></div><div class='loader'></div><div id='backgroundPopup'></div>");
	},
	
	showPopup : function(data,isadd,action){
		var popupStatus = 0;
		//jQuery(".loader").show();
		jQuery('#toPopup').replaceWith('<div id="toPopup">'+data+'</div>');
        jQuery('.quickview-index-view #toPopup a.button').each(function(){
            var href = jQuery(this).attr('href');
            if (typeof href !== typeof undefined && href !== false && href != '#') {
                jQuery(this).click(function(){
                    parent.location.href = href;
                    return false;
                })
            }
        })
		setTimeout(function(){
			if(popupStatus == 0) { 
				//jQuery(".loader").fadeOut('normal');
				jQuery('#toPopup').fadeIn(500); 
				jQuery('#backgroundPopup').css("opacity", "0.7");
				jQuery('#backgroundPopup').fadeIn(100);
				popupStatus = 1;
			}},200);
		if(isadd=='1')
		{
			setTimeout(function(){
			    if(popupStatus == 1) {
					jQuery("#toPopup").fadeOut("normal");
					jQuery("#backgroundPopup").fadeOut("normal");
					jQuery('#toPopup').replaceWith('<div id="toPopup"></div>');
					popupStatus = 0;
				}},1000000);
		}
		jQuery(".cart-continue").click(function() {
				if(popupStatus == 1) {
					jQuery("#toPopup").fadeOut("normal");
					jQuery("#backgroundPopup").fadeOut("normal");
					jQuery('#toPopup').replaceWith('<div id="toPopup"></div>');
					popupStatus = 0;
                    parent.jQuery.fancybox.close();
				}
			});
		jQuery("#backgroundPopup").click(function() {
				if(popupStatus == 1) {
					jQuery("#toPopup").fadeOut("normal");
					jQuery("#backgroundPopup").fadeOut("normal");
					jQuery('#toPopup').replaceWith('<div id="toPopup"></div>');
					popupStatus = 0;
				}
			});
		if(isadd=='0')
		{
			this.inPopup(action);
		}
	},
	
	inPopup : function(action){
	  jQuery('a.btn-cart').click(function(){
		eval(action);
	  });
	  jQuery('a.btn-cancel').click(function(){
				jQuery("#toPopup").fadeOut("normal");
				jQuery("#backgroundPopup").fadeOut("normal");
				jQuery('#toPopup').replaceWith('<div id="toPopup"></div>');
			
			});
	},
	
	updateSidebarCart : function() {
        if($$('.block-cart')[0]){
            var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'cart');
            new Ajax.Request(url, {
                method: 'post',
                onSuccess: function(transport) {
                    if(transport.responseText) {
                        var response = transport.responseText;
                        var holderDiv = document.createElement('div');
                        holderDiv = $(holderDiv);
                        holderDiv.innerHTML = response;
                        $$('.block-cart')[0].innerHTML = holderDiv.childElements()[0].innerHTML;
                    }
                }.bind(this)
            });
            return true;
        }
    },  

    
    updateCount : function(count) {
         var element = $$(this.miniCartClass)[0];
         if(element) {
              var pos = element.innerHTML.indexOf("(");
              if(pos >= 0 && count) {
                  element.innerHTML =  element.innerHTML.substring(0, pos) + count;    
              }
              else{
                  if(count)
                    element.innerHTML =  element.innerHTML + count;     
              }
         };
    },
	
    updateShoppingCart : function() {
        if($$('body.checkout-cart-index div.cart')[0]){
            var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'checkout');   
            new Ajax.Request(url, {
                method: 'post',
                onSuccess: function(transport) {
                   if(transport.responseText) {
                        var response = transport.responseText;
                        var holderDiv = document.createElement('div');
                        holderDiv = $(holderDiv);
                        holderDiv.innerHTML = response; 
                       $$('body.checkout-cart-index div.cart')[0].innerHTML = holderDiv.childElements()[0].innerHTML;
                    }       
                }.bind(this)
            });
         }
    }, 

    createMinicart: function() {
        var mnCart = $$(this.miniCartClass)[0];
        if(mnCart) {
            var wap_container = document.createElement('div');
            wap_container = $(wap_container);
            wap_container.className = 'dropdown-content';
            var container = document.createElement('div');
            container = $(container);
            container.id = 'arw-mini-cart';
            container.className = 'block block-cart';
            wap_container.appendChild(container);
            if(mnCart.parentNode){
                mnCart.parentNode.appendChild(wap_container);
                this.updateMinicart();
            }
            return;
        }
    },
    
    updateMinicart: function() {
               var url = ajaxCartObj.url.replace(ajaxCartObj.url.substring(ajaxCartObj.url.length-6, ajaxCartObj.url.length), 'reloadCart');
               var element = $('arw-mini-cart');
               new Ajax.Updater(element, url, {
                   method: 'post'
               }); 
    },
    
    showMinicart: function() {
	jQuery("#arw-mini-cart").stop(true, true).delay(200).slideDown(200, "easeOutBounce");
    },
    
    hideMinicart: function() {
        jQuery("#arw-mini-cart").stop(true, true).delay(200).fadeOut(500, "easeInCubic");
    },
    
    searchInPriceBox: function(parent, amEvent, element, idProduct) {
        if(parent.getElementsByClassName('special-price')[0])
        {
            var child = parent.getElementsByClassName('special-price')[0];
            var elementInt = 1;
        }
        else
        {
            var child = parent.getElementsByClassName('price-box')[0];
            var elementInt = 0;
        }

        if(child) {
            var childNext = child.childElements()[elementInt];
            if(childNext){
                idProduct = childNext.id.replace(/[^\d]/gi, '');
            }
            if(!idProduct || idProduct == '') {
                child.childElements()[0].childElements().each(function(childNext) {
                    idProduct = childNext.id.replace(/[a-z-]*/, '');
                    if(parseInt(idProduct) > 0) {
                        return idProduct;     
                    }    
                }.bind(this));
            }
            if(!idProduct || idProduct == '') {
                child.select(".price").each(function(childNext) {
                if(childNext.id)
                    idProduct = childNext.id.replace(/[a-z-]*/, '');
                    if(parseInt(idProduct) > 0) {
                        return idProduct;     
                    }    
                }.bind(this));
            }
            if(parseInt(idProduct) > 0) {
                 var tmp = parseInt(idProduct); 
                 this.sendAjax(tmp, '', amEvent, element);
                 return idProduct;
            }
            else {
                idProduct = '';    
            }
        } 
        return ''; 
    } 
}
function searchIdAndSendAjax(event) {
    var element = Event.element(event);
	//showAnimation(element);
    event.preventDefault();
    var addToLinc = 'add-to-links';
    
    if($('confirmBox')) {
        jQuery(function($) {
            $.confirm.hide();
        })
    }
    if(!element.hasClassName('button')) {
         element = $(element.parentNode.parentNode);    
    }
    var amEvent = element.getAttribute('amEvent');
    var idProduct = '';
    var el = $(element.parentNode.parentNode);
    if(el) {
        var idProduct = ajaxCartObj.searchInPriceBox(el, amEvent, element, idProduct);     
    }    
    if(idProduct == '') {
        var el = $(element.parentNode.parentNode.parentNode);
        if(el) {
            var idProduct = ajaxCartObj.searchInPriceBox(el, amEvent, element, idProduct);     
        }
    }   
    if(idProduct == '') {
        var el = $(element.parentNode);
        var child  = el.getElementsByClassName(addToLinc)[0];
        if(child) {
            var childNext = child.childElements()[0];
            if(childNext) {
                var childNext = childNext.childElements()[0];    
            }
            if(childNext) {
                var idProduct = childNext.href.match(/product(.?)+/)[0].replace(/[^\d]/gi, '');
            }
            if(parseInt(idProduct) > 0) {
                 var tmp = parseInt(idProduct); 
                 ajaxCartObj.sendAjax(tmp, '', amEvent, element);
                 return true;
            }
             else{
                idProduct = '';    
            }
        }
    }
    if(idProduct == '' && $$("input[name='product']")[0] && $$("input[name='product']")[0].value) {
        idProduct = $$("input[name='product']")[0].value;
        if(parseInt(idProduct) > 0) {
             var tmp = parseInt(idProduct); 
             ajaxCartObj.sendAjax(tmp, '', amEvent, element);
             return true;
        }     
    }
    
    if(idProduct == '' && amEvent) {
        var productString = '/product/';
        var posStart = amEvent.indexOf(productString);
        if(posStart) {
            var posFinish = amEvent.indexOf('/', posStart + productString.length);
            if(posFinish) {
                var idProduct = amEvent.substring(posStart + productString.length, posFinish);
                   if(parseInt(idProduct) > 0) {
                         var tmp = parseInt(idProduct); 
                         ajaxCartObj.sendAjax(tmp, '', amEvent, element);
                         return true;
                   }
                else {
                    idProduct = '';    
                }
            }
        }
    }   
    if(idProduct == '') {
         //if IE7
        if (document.all && !document.querySelector) {
           amEvent = amEvent.substring(21, amEvent.length-2)
        }
        eval(amEvent);    
    }
}

function ajaxCartShoppCartLoad(buttonClass){
    $$(buttonClass).each(function(element){
            if(element.getAttribute('onclick')){
                var attr = document.createAttribute('amEvent');
                attr.nodeValue =  element.getAttribute('onclick').toString(); 
                element.attributes.setNamedItem(attr);
            }        
            element.onclick = '';
	    element.stopObserving('click');
            Event.observe(element, 'click', searchIdAndSendAjax );
    }.bind(this));
}

document.observe("dom:loaded", function() {
  ajaxCartObj.createMinicart();
  ajaxCartObj.createPopup();
  ajaxCartShoppCartLoad('.btn-cart');
  ajaxCartShoppCartLoad('.link-cart');
});

jQuery(document).on('click', '#arw_remove_product', function() {
    var strArr=[];
    jQuery('.remove_product[type="checkbox"]:checked').each(function(index) {
        strArr[index]=jQuery(this).val();
    });
    var strId=strArr.join();
    if(strId){
        if(confirm("Are you sure you would like to remove this items from the shopping cart?")){
            var delUrl= ajaxCartObj.url.replace('ajaxcart/ajax/index', 'ajaxcart/ajax/delete');
            jQuery.ajax({
                type:'post',
                dataType : 'json',
                data:'productId='+strId,
                url:delUrl,
                beforeSend:function(){
                    jQuery(".loader").show();
                },
                success: function(data,status){
                    ajaxCartObj.showPopup(data.product_name,1);
                    ajaxCartObj.updateSidebarCart();
                    ajaxCartObj.updateShoppingCart();
                    ajaxCartObj.updateMinicart();
                    ajaxCartObj.updateCount(data.count);
                },
                complete:function(){
                    jQuery(".loader").hide();
                }
            });
        }else{
            return false;
        }
    } else
    {
        alert('Please choose products');
    }
});