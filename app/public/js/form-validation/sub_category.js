var FormValidation = function () {
    
    var handleFormSubmit = function () {
        var form = $('#m_sub_category');

        form.validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50,
                },
            },
            messages: {
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.',
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