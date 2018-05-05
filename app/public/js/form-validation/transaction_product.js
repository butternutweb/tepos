var FormValidation = function () {
    
    var handleFormSubmit = function () {
        var form = $('#m_transaction_product');

        form.validate({
            rules: {
                qty: {
                    required: true,
                    digits: true,
                    min: 0,
                },
                note: {
                    maxlength: 50,
                },
                product_id: {
                    required: true,
                    digits: true
                },
            },
            messages: {
                qty: {
                    required: 'The quantity field is required.',
                    digits: 'The quantity must be an integer.',
                    min: 'The quantity must be at least {0}.'
                },
                note: {
                    maxlength: 'The note may not be greater than {0} characters.',
                },
                product_id: {
                    required: 'The product field is required.',
                    digits: 'The product must be an integer.'
                }
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