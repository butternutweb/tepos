var FormValidation = function () {
    
    var handleFormSubmit = function () {
        var form = $('#m_transaction');

        form.validate({
            rules: {
                date: {
                    required: true,
                    date: true,
                },
                note: {
                    maxlength: 50,
                },
                status_id: {
                    required: true,
                    digits: true
                },
                staff_id: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                date: {
                    required: 'The date field is required.',
                    date: 'The date is not a valid date.'
                },
                note: {
                    maxlength: 'The note may not be greater than {0} characters.',
                },
                status_id: {
                    required: 'The status field is required.',
                    digits: 'The status must be an integer.'
                },
                staff_id: {
                    required: 'The staff field is required.',
                    digits: 'The staff must be an integer.'
                }
            },
            invalidHandler: function(event, validator) {     
                mApp.scrollTo(form, -200);
            },
        });
    }

    var handleStaffFormSubmit = function () {
        var form = $('#m_staff_transaction');

        form.validate({
            rules: {
                date: {
                    required: true,
                    date: true,
                },
                note: {
                    maxlength: 50,
                },
                status_id: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                date: {
                    required: 'The date field is required.',
                    date: 'The date is not a valid date.'
                },
                note: {
                    maxlength: 'The note may not be greater than {0} characters.',
                },
                status_id: {
                    required: 'The status field is required.',
                    digits: 'The status must be an integer.'
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
            handleStaffFormSubmit();
        }
    };
}();

jQuery(document).ready(function () {
    FormValidation.init();
});