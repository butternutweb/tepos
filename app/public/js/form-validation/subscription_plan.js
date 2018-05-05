var FormValidation = function () {

    var handleFormSubmit = function () {
        var form = $('#m_subscription_plan');

        form.validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 30,
                },
                store_number: {
                    required: true,
                    digits: true,
                    min: 0,
                },
                duration_day: {
                    required: true,
                    digits: true,
                    min: 0,
                },
                price: {
                    required: true,
                    digits: true,
                    min: 0,
                },
            },
            messages: {
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.',
                },
                store_number: {
                    required: 'The store number field is required.',
                    digits: 'The store number must be an integer.',
                    min: 'The store number must be at least {0}.'
                },
                duration_day: {
                    required: 'The duration field is required.',
                    digits: 'The duration must be an integer.',
                    min: 'The duration must be at least {0}.'
                },
                price: {
                    required: 'The price field is required.',
                    digits: 'The price must be an integer.',
                    min: 'The price must be at least {0}.'
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