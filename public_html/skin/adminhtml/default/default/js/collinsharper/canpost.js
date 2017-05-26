document.observe('dom:loaded', function() {
    var restrict = document.getElementById('restrict_shipping_methods');
    var methods = document.getElementById('allowed_shipping_methods');

    restrict.onchange = function() {
        if (this.options[this.selectedIndex].value == '0') {
            methods.setAttribute('disabled', true);
        } else {
            methods.removeAttribute('disabled');
        }
    };

    var form = document.getElementById('product_edit_form');
    form.addEventListener('submit', function() {
        this.getElementById('allowed_shipping_methods').removeAttribute('disabled');
    }, false);

    restrict.onchange();
});
