var FormValidation = function () {
    
    var handleFormSubmit = function () {
        var form = $('#m_subscription_transaction');

        form.validate({
            rules: {
                date: {
                    required: true,
                    date: true,
                },
                payment_method: {
                    required: true,
                    maxlength: 30,
                },
                owner_id: {
                    required: true,
                    digits: true
                },
                subscription_plan_id: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                date: {
                    required: 'The date field is required.',
                    date: 'The date is not a valid date.'
                },
                payment_method: {
                    required: 'The payment method field is required.',
                    maxlength: 'The payment method may not be greater than {0} characters.',
                },
                owner_id: {
                    required: 'The owner field is required.',
                    digits: 'The owner must be an integer.'
                },
                subscription_plan_id: {
                    required: 'The subscription plan field is required.',
                    digits: 'The subscription plan must be an integer.'
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