var exclude_services = [];
var message_no_location;
var message_only_canada;
var allow_update_ch_price = true;
var win;

function showGmap(base_url) {
    if ($('postcode')) {
        var regexp = /\w\d\w\s?\d\w\d/i;
        if (regexp.exec($('postcode').value)) {
            new Ajax.Request(base_url + 'canpost/gmap/index', {
              method:'post',
              parameters:{
                  postcode: $('postcode').value
              },
              onSuccess: function(response) {
                  win = new Window({className: "cpost", width:704, height:502, zIndex: 200, resizable: false, title: Translator.translate("Deliver to Post Office"), showEffect:Effect.BlindDown, hideEffect: Effect.SwitchOff, draggable:false, wiredDrag: false});
                  win.getContent().innerHTML = response.responseText;
                  $('_overlay').show();
                  win.setStatusBar(""); 
                  win.showCenter(); 
                  Windows.addObserver({
                    onClose: function(eventName, win) {
                      if ($('option_D2PO') && (!$('office') || $('office').value == '' || $('office').value == 0)) {
                          $('option_D2PO').checked = false;
                          $('update_office_list').disable();
                      }
                      $('_overlay').hide();
                    }
                  });
              }
            });
        } else {
            alert(message_only_canada);
            if ($('option_D2PO')) {
              $('option_D2PO').checked = false;
            }
            $('shipping-method-please-wait').hide();
            $$('#shipping-method-buttons-container button').invoke('show');
        }
    } 
}
function chReloadRates(base_url)
{
    if ($$('#shipping-method-buttons-container button')) {
        $$('#shipping-method-buttons-container button').invoke('hide');
    }
    if ($('shipping-method-please-wait')) {
        $('shipping-method-please-wait').show();
    }
    if ($('option_D2PO') && $('option_D2PO').checked) {
        $('update_office_list').enable();
        showGmap(base_url);        
        if ($('shipping-method-please-wait')) {
            $('shipping-method-please-wait').hide();
            $$('#shipping-method-buttons-container button').invoke('show');
        }
    } else {
        $('update_office_list').disable();
        $('cp_office_name').innerHTML = '';
        $('cp_office_address').innerHTML = '';
        $('office').value = '';
        $('update_office_list').disable();
        chUpdateRates(base_url);
    }
}
function chUpdateRates(base_url)
{
    $$('input[type="checkbox"]').each(function(el){ 
        el.disabled = true; 
    });
    if ($$('#shipping-method-buttons-container button')) {
        $$('#shipping-method-buttons-container button').invoke('hide');
    }
    if ($('shipping-method-please-wait')) {
        $('shipping-method-please-wait').show();
    }
    new Ajax.Request(base_url + 'canpost/shipping/getRates', {
      method:'post',
      parameters:{
          coverage: ($('option_COV') && $('option_COV').checked) ? 1 : 0, 
          signature:($('option_SO') && $('option_SO').checked) ? 1 : 0, 
          card_for_pickup:($('option_HFP') && $('option_HFP').checked) ? 1 : 0,
          do_not_safe_drop:($('option_DNS') && $('option_DNS').checked) ? 1 : 0,
          leave_at_door:($('option_LAD') && $('option_LAD').checked) ? 1 : 0,
          cod:($('option_COD') && $('option_COD').checked) ? 1 : 0,
          deliver_to_post_office:($('option_D2PO') && $('option_D2PO').checked) ? 1 : 0,
          office_id: ($('office')) ? $('office').value : '',
          service_code: $$('input:checked[type="radio"][name="shipping_method"]') ? $$('input:checked[type="radio"][name="shipping_method"]').pluck('value') : ''
      },

      requestHeaders: {Accept: 'application/json'},
      onSuccess: function(response) {
          var json = response.responseText.evalJSON();
          for (var code in json.rates) {
              if ($('s_label_method_chcanpost2module_' + code) && allow_update_ch_price) {
                $('s_label_method_chcanpost2module_' + code).innerHTML = json.rates[code].title + ' <b>' + json.rates[code].price + '</b>';
              }
              //for one step checkout
              if ($$('.onestepcheckout-totals') && $$('.onestepcheckout-totals')[0]) {
                var updated_shipping = false;  
                var total  = 0;
                for (var i=0; i < $$('.onestepcheckout-totals tr td').length; i++) {                    
                    if ($$('.onestepcheckout-totals tr td')[i].innerHTML.indexOf('Shipping') > 0) {
                        $$('.onestepcheckout-totals tr td')[i+1].innerHTML = json.rates[code].price;
                        updated_shipping = true;
                    }
                }
                if (allow_update_ch_price) {
                    if (!updated_shipping) {
                        var tempHtml = '';
                        for (var i=0; i < $$('.onestepcheckout-totals tbody tr').length; i++) {                        
                            if (i == ($$('.onestepcheckout-totals tbody tr').length-1)) {
                                tempHtml += '<tr><td class="title">Shipping</td><td class="value">' + json.rates[code].price + '</td></tr>';
                            }
                            tempHtml += '<tr>' + $$('.onestepcheckout-totals tbody tr')[i].innerHTML + '</tr>';
                        }
                        $$('.onestepcheckout-totals tbody')[0].innerHTML = tempHtml;
                    }

                    for (var i=0; i < $$('.onestepcheckout-totals tbody tr td span.price').length; i++) {
                        if (i == ($$('.onestepcheckout-totals tbody tr td span.price').length - 1)) {
                            var curIdentifier = $$('.onestepcheckout-totals tbody tr td span.price')[i].innerHTML.replace(/[0-9\.,]+/ig,'')
                            $$('.onestepcheckout-totals tbody tr td span.price')[i].innerHTML = curIdentifier + (Math.round(100*total)/100).toFixed(2);
                        } else {
                            var tmpValue = $$('.onestepcheckout-totals tbody tr td span.price')[i].innerHTML.replace('$', '');
                            var tmpElementLabel = $$('.onestepcheckout-totals tbody tr td span.price')[i].up().up().up().down("td").innerHTML.toLowerCase();
                            // lets try to only get total rows that are not grand totals.
                            if((tmpValue.match(/(\$)/g) || tmpValue.match(/(\.)/g) || tmpValue.match(/(,)/g)) && tmpElementLabel.indexOf('grand') == -1) {
                                var value = parseFloat($$('.onestepcheckout-totals tbody tr td span.price')[i].innerHTML.replace(/[^0-9\.\,]+/ig, ''));
                                total +=  isNaN(value) ? 0 : value;
                            }

                        }
                    }
                }
              }
          }
          $$('input[type="checkbox"]').each(function(el){ 
                el.disabled = false; 
          });
          try {
              for (var i = 0; i < json.conflicted_options.length; i++) {
                  if ($('option_' + json.conflicted_options[i])) {
                      $('option_' + json.conflicted_options[i]).disabled = true;
                  }
              }
              for (var i in json.selected_options) {
                  if ($('option_' + json.selected_options[i])) {
                      $('option_' + json.selected_options[i]).checked = true;
                  }
              }
              for (var i in exclude_services) {
                  if ($('option_' + exclude_services[i])) {
                      $('option_' + exclude_services[i]).disabled = true;
                      $('option_' + exclude_services[i]).checked = false;
                  }
              }
              for (var i = 0; i < json.readonly_options.length; i++) {
                  if ($('option_' + json.readonly_options[i])) {
                      $('option_' + json.readonly_options[i]).disabled = true;
                  }
              }
              for (var opt in json.params) {
                  if ($('option_' + opt) && json.params[opt] == 1) {
                      $('option_' + opt).checked = true;
                  }
                  if (opt == 'office' && $('office')) {
                      $('office').value = json.params[opt];
                  }
                  if (opt == 'office_name' && $('cp_office_name')) {
                      $('cp_office_name').innerHTML = json.params[opt];
                  }
                  if (opt == 'office_address' && $('cp_office_address')) {
                      $('cp_office_address').innerHTML = json.params[opt];
                  }
              }
          }catch(err) {
          //Occasionally we send bad methods and CP is confused
          }
          if ($('shipping-method-please-wait')) {
              $('shipping-method-please-wait').hide();
          }
          if ($$('#shipping-method-buttons-container button')) {
              $$('#shipping-method-buttons-container button').invoke('show');
          }
      }
    });
}
function getServiceOptions(service_code, base_url, to_post_office)
{
    if ($$('#shipping-method-buttons-container button')) {
        $$('#shipping-method-buttons-container button').invoke('hide');
    }
    if ($('shipping-method-please-wait')) {
        $('shipping-method-please-wait').show();
    }
    $$('input[type="checkbox"]').each(function(el){ 
        el.disabled = true; 
        el.checked = false; 
    });
    var postcode = ($('postcode')) ? $('postcode').value : '';
    new Ajax.Request(base_url + 'canpost/shipping/getServices/service_code/' + service_code + '/postal_code/' + postcode, {
      method:'get',
      requestHeaders: {Accept: 'application/json'},
      onSuccess: function(response) {
          var json = response.responseText.evalJSON();
          try{
          if (json != '') {
              $$('input[type="checkbox"]').each(function (el) {
                  el.disabled = false;
                  el.checked = false;
              });
              for (var i = 0; i < json.selected_options.length; i++) {
                  if ($('option_' + json.selected_options[i])) {
                      $('option_' + json.selected_options[i]).checked = true;
                  }
              }
              for (var i = 0; i < json.exclude_services.length; i++) {
                  if ($('option_' + json.exclude_services[i])) {
                      $('option_' + json.exclude_services[i]).disabled = true;
                      $('option_' + json.exclude_services[i]).checked = false;
                  }
              }
              for (var i = 0; i < json.readonly_options.length; i++) {
                  if ($('option_' + json.readonly_options[i])) {
                      $('option_' + json.readonly_options[i]).disabled = true;
                  }
              }
              for (var opt in json.saved_params) {
                  if ($('option_' + opt) && json.saved_params[opt] == 1) {
                      $('option_' + opt).checked = true;
                  }
                  if (opt == 'office' && $('office')) {
                      $('office').value = json.saved_params[opt];
                  }
                  if (opt == 'office_name' && $('cp_office_name')) {
                      $('cp_office_name').innerHTML = json.saved_params[opt];
                  }
                  if (opt == 'office_address' && $('cp_office_address')) {
                      $('cp_office_address').innerHTML = json.saved_params[opt];
                  }
              }
              exclude_services = json.exclude_services;
              chShowOptions();
              if ($('option_D2PO') && to_post_office) {
                  $('option_D2PO').checked = true;
                  $('update_office_list').enable();
              }
          }
      }catch(err) {

          }
          chUpdateRates(base_url);
      }
    });
}
function chShowOptions(){
        if ($('shipping_options_title')) {
            //BC: this should update the elements based on what kind of CP shipping method is selected
            $('shipping_options_title').show();
            $$('.shipping_method_options').each(function(el){
                    el.show();
            });
        }
}
function chHideOptions(){
        if ($('shipping_options_title')) {
            //BC: this should be invoked if another shipping method is clicked
            $('shipping_options_title').hide();
            $$('.shipping_method_options').each(function(el){
                    el.hide();
            });
        }
}
function chDisableOptions() {
    $$('.osc_shipping_method_options input').each(function(el){
            el.checked = false;
            el.disable();
    });
}


function showMethodDescription() {
    $$('.ch_cp_rate').each(function(el){
        var method_description = el.readAttribute('ref');
        if (method_description.length > 0) {
            el.update(el.innerHTML + '<span class="method-description">' + method_description + '</span>');

        }
    });
}

