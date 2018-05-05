var FormValidation = function () {
    
    var handleFormSubmit = function () {
        var form = $('#m_store_product');

        form.validate({
            rules: {
                selling_price: {
                    required: true,
                    digits: true,
                    min: 0,
                },
                product_id: {
                    required: true,
                    digits: true,
                },
            },
            messages: {
                selling_price: {
                    required: 'The selling price field is required.',
                    digits: 'The selling price must be an integer.',
                    min: 'The selling price must be at least {0}.',
                },
                product_id: {
                    required: 'The product field is required.',
                    digits: 'The product must be an integer.'
                },
            },
            invalidHandler: function(event, validator) {     
                mApp.scrollTo(form, -200);
            },
        });
    }
    
    return {
        init: function () {
            handleFormSubmit();
        }
    };
}();

jQuery(document).ready(function () {
    FormValidation.init();
});