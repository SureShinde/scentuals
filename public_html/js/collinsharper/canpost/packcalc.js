function recalcWeightAndCustoms() {
	var totalWeight = 0;
	var totalCustomsValue = 0;
	for (var i = 0; i < $$('.package-block').length; i++) {
		var packageBlock = $$('.package-block')[i];
		var packageId = packageBlock.id.match(/\d$/)[0];
	        var containerWeight = packageBlock.select('[name="container_weight"]')[0];
        	var containerCustomsValue = packageBlock.select('[name="package_customs_value"]')[0];
        	containerWeight.value = 0;
        	containerCustomsValue.value = 0;
        	packageBlock.select('.grid tbody tr').each(function(item) {
			if (item.select('[type="checkbox"]')[0].checked) {
            			var itemId = item.select('[type="checkbox"]')[0].value;
	                	var qtyValue  = parseFloat(item.select('[name="qty"]')[0].value);
            			if (isNaN(qtyValue) || qtyValue <= 0) {
                			qtyValue = 1;
		                	item.select('[name="qty"]')[0].value = qtyValue;
           			}
            			var itemWeight = parseFloat(getElementText(item.select('.weight')[0]));
            			containerWeight.value = parseFloat(containerWeight.value) + (itemWeight * qtyValue);
            			var itemCustomsValue = parseFloat(item.select('[name="customs_value"]')[0].value) || 0;
            			containerCustomsValue.value = parseFloat(containerCustomsValue.value) + itemCustomsValue * qtyValue;
			}
        	});
		totalWeight = parseFloat(totalWeight) + parseFloat(containerWeight.value);
		totalCustomsValue = parseFloat(totalCustomsValue) + parseFloat(containerCustomsValue.value);
	} 
	var savePackagesBtn = $$('.SavePackagesBtn')[0];
	savePackagesBtn.writeAttribute('onclick', '');
        savePackagesBtn.observe('click', checkDimensions);
}
function getElementText(el) {
        if ('string' == typeof el.textContent) {
            return el.textContent;
        }
        if ('string' == typeof el.innerText) {
            return el.innerText;
        }
        return el.innerHTML.replace(/<[^>]*>/g,'');
}
function checkDimensions(e) {
	if ($$('[name="container_length"]')[1].value.length > 0 && $$('[name="container_width"]')[1].value.length > 0 && $$('[name="container_height"]')[1].value.length > 0) {
		packaging.confirmPackaging();
	} else {
		alert('Please specify dimensions');
	}
}
document.observe("dom:loaded", function() {
	if($$('label[for="create_shipping_label"]') != '') {
		$$('label[for="create_shipping_label"]')[0].update('Create Shipping Label / Pack Boxes');
}
});
