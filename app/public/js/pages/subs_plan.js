jQuery(document).ready(function () {
    $('.btn-purchase').click(function(e){
        snap.show();
        $.ajax({
            type: 'get',
            url: 'subs-plan/getToken',
            data: {
                'id': $(this).attr('data-plan')
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
                snap.pay(data.token,{
                    onSuccess: function(result){toastr.success('Transaction Success','Your transaction has been approved');},
                    onPending: function(result){toastr.success('Transaction Pending','Please proceed to next step');},
                    onError: function(result){toastr.error('Transaction Success','Your transaction has been approved');}
                });
            },
            error: function(response){
                console.log(response);
            }
            
        });
    })
});