var Pages = function () {
    
    var run = function () {

        $('#m_save_form').submit(function(e) {
            e.preventDefault();
            if ($('input[name="amount"]').val()!==null && parseInt($('input[name="amount"]').val()) < parseInt($('#amount_paid').html())){
                $('input[name="amount"]').focus();
            } else {
                $(this).append('<input type="hidden" name="amount" value="'+$('input[name="amount"]').val()+'">');
                $(this).append('<input type="hidden" name="note" value="'+$('textarea[name="note"]').val()+'">');
                $(this).unbind('submit').submit();
            };
        });
        $('#checkout_btn').click(function(){
            $('#m_save_form').append('<input type="hidden" name="do_checkout" value="1">');
            $('#m_save_form').submit();
        })
        $('.m_touchspin').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
        });

        $('.bootstrap-touchspin-down, .bootstrap-touchspin-up').mousedown(function(e){
            updateTransaction($(e.target).closest('.m-bootstrap-touchspin-brand').find('input[name="product_id"]').val(),$(e.target).closest('.m-bootstrap-touchspin-brand').find('.m_touchspin.form-control').val(),$(e.target).closest('.m-bootstrap-touchspin-brand').find('span.price').html().replace('.',''));
        });
        $('.m_touchspin.form-control').blur(function(){
            updateTransaction($(this).closest('.m-bootstrap-touchspin-brand').find('input[name="product_id"]').val(),$(this).val(),$(this).closest('.m-bootstrap-touchspin-brand').find('span.price').html().replace('.',''));
        });
        function updateTransaction(product_id, product_number, price) {
            $.ajax({
                type: 'post',
                url: '/transaction/addProduct',
                data: {
                    'product_id': product_id,
                    'product_number': product_number,
                    'product_price': price
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $('#amount_paid').html(data.sales);
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
                },
                error: function(err){
                    console.log(err);
                }
            });
        };
        //=====================================================
        $('#print-btn').click(function(){
            $('#print_inv').modal('show');
        })
        $('#print_inv').on('click','.btn-print',function(){
            window.open("/transaction/invoice/"+$(this).attr('data-transaction'), "_blank", "toolbar=no,scrollbars=no,resizable=yes");
            $('#print_inv').modal('hide');
        })
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