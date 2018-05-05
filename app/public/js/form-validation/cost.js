var FormValidation = function () {
    
    var handleFormSubmit = function () {
        var form = $('#m_cost');

        form.validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50,
                },
                amount: {
                    required: true,
                    digits: true,
                    min: 0,
                },
                date: {
                    required: true,
                    date: true,
                },
            },
            messages: {
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.',
                },
                amount: {
                    required: 'The amount field is required.',
                    digits: 'The amount must be an integer.',
                    min: 'The amount must be at least {0}.',
                },
                date: {
                    required: 'The date field is required.',
                    date: 'The date is not a valid date.'
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