var FormValidation = function () {

    var handleFormSubmit = function () {
        var form = $('#m_product');

        form.validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50
                },
                sku: {
                    maxlength: 30,
                },
                note: {
                    maxlength: 50,
                },
                capital_price: {
                    digits: true,
                    min: 0
                },
                sub_category_id: {
                    required: true,
                    digits: true,
                },
            },
            messages: {
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.'
                },
                sku: {
                    maxlength: 'The SKU may not be greater than {0} characters.'
                },
                note: {
                    maxlength: 'The note may not be greater than {0} characters.'
                },
                capital_price: {
                    digits: 'The capital price must be an integer.',
                    min: 'The capital price must be at least {0}.',
                },
                sub_category_id: {
                    required: 'The sub category field is required.',
                    digits: 'The sub category must be an integer.'
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