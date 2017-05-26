tabSlide = Class.create();
tabSlide.prototype= {
    selector:'.slider-tab',
    effect:'',
    url   :'',
    duration:'',
    from	:'',
    to		:'',
    direction:'',
    identifier:'',
    isProductViewT : 0,
    initialize: function(options){
        this.effect	= options['effect'];
        this.url	= options['url'];
        this.duration=options['duration'];
        this.from=options['from'];
        this.to=options['to'];
        this.direction= options['direction'];
        this.identifier=options['identifier'];
        this.icon=options['icon'];
        this.isProductViewT=options['is_product_view_tab'];
        setStyleAjax(this.icon);

    },
    run : function(selector)
    {
        $$(this.selector+' li > span').each(this.initTab.bind(this));
    },
    initTab: function(el) {
        if ($(el.parentNode).hasClassName('active')) {
            this.showContent(el);
        }
        if($(el.parentNode.parentNode).childElements().length>1){
            el.observe('click', this.showContent.bind(this, el));
        }
    },
    showAnimation: function(id)
    {
        $(id+'_contents').up().down('div.ajax_loading_tab').setStyle({
            display:'block'
        });
    },
    hideAnimation: function(id)
    {
        $(id+'_contents').up().down('div.ajax_loading_tab').hide();
    },
    showProduct: function(catId,id){
        var postData = 'ajax_tab_id=' + catId + '&countdown=' + $(id).getAttribute('data-enable-countdown').toString();

            new Ajax.Request(this.url,{
                method:'post',
                postBody:postData,
                onCreate: function()
                {
                    this.showAnimation(id);
                }.bind(this),
                onComplete: function()
                {
                    if((typeof ajaxCartShoppCartLoad != "undefined")&&(!this.isProductViewT)){
                        ajaxCartShoppCartLoad('button.btn-cart');
                    }
                    arexworks.Frontend.initTooltip();
                    arexworks.Frontend.countdown();
                    this.hideAnimation(id);
                }.bind(this),
                onSuccess: function(transport){
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.error) {
                            alert(response.error);
                        }
                        else{

                        }
                        /* this.hideAnimation(); */
                        /* $('backgroundajax').hide();
                         $('ajax_loading').hide(); */
                        var content=$(id+'_contents');
                        var tab_id	= id+'_contents';
                        content.replace('<div id="'+tab_id+'">'+response.productlist+'</div>');
                        content.show();
                    }
                }.bind(this)
            });

    },
    showContent: function(a) {
        var li = $(a.parentNode), ul = $(li.parentNode);
        ul.select('li').each(function(el){
            var contents = $(el.id+'_contents');
            if (el==li) {
                //catId = parseInt(el.id.replace(/[^\d]/gi, ''));
                lastIndex=parseInt(el.id.lastIndexOf('_'));
                index=parseInt(el.id.indexOf('_'))+1;
                catId=el.id.substring(index,lastIndex);
                if($(el.id+'_contents').innerHTML.length < 5)
                {
                    this.showProduct(catId,el.id);
                }
                el.addClassName('active');
                contents.show();
                setTimeout(function(){
                    arw_equal_height(jQuery(contents).find('.products-grid:not(.products-slide) li.item .product-name'));
                    equalheight2(jQuery(contents).find('.products-slide'),'.product-name');
                },500);
            } else {
                el.removeClassName('active');
                contents.hide();
            }
        }.bind(this));
    }
}
function setStyleAjax(data)
{
    if ($$('.ajax_loading_tab') != undefined){
        $$('.ajax_loading_tab').each(function(ele) {
            ele.setStyle({
                backgroundImage: 'url(' + data + ')'
            });
        });
    }
}
