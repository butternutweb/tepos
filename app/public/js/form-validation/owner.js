var FormValidation = function () {

    var handleAdminCreateFormSubmit = function () {
        var form = $('#m_admin_create_owner');

        form.validate({
            rules: {
                username: {
                    required: true,
                    alphanumeric: true,
                    rangelength: [4, 50]
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 255,
                },
                password: {
                    required: true,
                    minlength: 4,
                    equalTo: "input[name='password_confirmation']"
                },
                name: {
                    required: true,
                    maxlength: 30
                },
                phone: {
                    required: true,
                    maxlength: 30
                },
                status_id: {
                    required: true,
                    digits: true,
                },
            },
            messages: {
                username: {
                    required: 'The username field is required.',
                    alphanumeric: 'The username may only contain letters and numbers.',
                    rangelength: 'The username must be between {0} and {1} characters.',
                },
                email: {
                    required: 'The email field is required.',
                    email: 'The email must be a valid email address.',
                    maxlength: 'The email may not be greater than {0} characters.'
                },
                password: {
                    required: 'The password field is required.',
                    minlength: 'The password must be at least {0} characters.',
                    equalTo: 'The password confirmation does not match.',
                },
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.'
                },
                phone: {
                    required: 'The phone field is required.',
                    maxlength: 'The phone may not be greater than {0} characters.'
                },
                status_id: {
                    required: 'The status field is required.',
                    digits: 'The status must be an integer.'
                },
            },
            invalidHandler: function(event, validator) {     
                mApp.scrollTo(form, -200);
            },
        });
    }

    var handleAdminEditFormSubmit = function () {
        var form = $('#m_admin_edit_owner');

        form.validate({
            rules: {
                username: {
                    required: true,
                    alphanumeric: true,
                    rangelength: [4, 50]
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 255,
                },
                password: {
                    minlength: 4,
                    equalTo: "input[name='password_confirmation']"
                },
                name: {
                    required: true,
                    maxlength: 30
                },
                phone: {
                    required: true,
                    maxlength: 30
                },
                status_id: {
                    required: true,
                    digits: true,
                },
            },
            messages: {
                username: {
                    required: 'The username field is required.',
                    alphanumeric: 'The username may only contain letters and numbers.',
                    rangelength: 'The username must be between {0} and {1} characters.',
                },
                email: {
                    required: 'The email field is required.',
                    email: 'The email must be a valid email address.',
                    maxlength: 'The email may not be greater than {0} characters.'
                },
                password: {
                    minlength: 'The password must be at least {0} characters.',
                    equalTo: 'The password confirmation does not match.',
                },
                name: {
                    required: 'The name field is required.',
                    maxlength: 'The name may not be greater than {0} characters.'
                },
                phone: {
                    required: 'The phone field is required.',
                    maxlength: 'The phone may not be greater than {0} characters.'
                },
                status_id: {
                    required: 'The status field is required.',
                    digits: 'The status must be an integer.'
                },
            },
            invalidHandler: function(event, validator) {     
                mApp.scrollTo(form, -200);
            },
        });
    }

    return {
        init: function () {
            handleAdminCreateFormSubmit();
            handleAdminEditFormSubmit();
        }
    };
}();

jQuery(document).ready(function () {
    FormValidation.init();
});    