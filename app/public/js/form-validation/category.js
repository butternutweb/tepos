var FormValidation = function () {
    
    var handleAdminFormSubmit = function () {
        var form = $('#m_admin_category');

        form.validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50,
                },
                owner_id: {
                    required: true,
                    digits: true,
                }
            },
            messages: {
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.',
                },
                owner_id: {
                    required: 'The owner field is required.',
                    digits: 'The owner must be an integer.'
                },
            },
            invalidHandler: function(event, validator) {     
                mApp.scrollTo(form, -200);
            },
        });
    }

    var handleOwnerFormSubmit = function () {
        var form = $('#m_owner_category');

        form.validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50,
                }
            },
            messages: {
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.',
                }
            },
            invalidHandler: function(event, validator) {     
                mApp.scrollTo(form, -200);
            },
        });
    }

    return {
        init: function () {
            handleAdminFormSubmit();
            handleOwnerFormSubmit();
        }
    };
}();

jQuery(document).ready(function () {
    FormValidation.init();
});    