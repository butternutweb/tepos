var Pages = function () {
    
    var run = function () {

        $('#m_save_form').submit(function(e) {
            e.preventDefault();
            var note = $(e.target).closest('#m_header_nav').find('#m_note').val();
            var note_form = $(e.target).find('#m_save_form_note');
            note_form.val(note);
            $(this).unbind('submit').submit();
        });

        $('.m_touchspin').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
        });

        $('.bootstrap-touchspin-down, .bootstrap-touchspin-up').mousedown(function(e){
            updateTransaction($(e.target).closest('.m-bootstrap-touchspin-brand').find('input[name="product_id"]').val(),$(e.target).closest('.m-bootstrap-touchspin-brand').find('.m_touchspin.form-control').val());
        });
        $('.m_touchspin.form-control').blur(function(){
            updateTransaction($(this).closest('.m-bootstrap-touchspin-brand').find('input[name="product_id"]').val(),$(this).val());
        });
        function updateTransaction(product_id, product_number) {
            $.ajax({
                type: 'post',
                url: '/transaction/addProduct',
                data: {
                    'product_id': product_id,
                    'product_number': product_number,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-bottom-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    
                    toastr.success(data.success);
                }
            });
        };
    };

    return {
        init: function () {
            run();
        },
    };
}();

jQuery(document).ready(function () {
    Pages.init();
});