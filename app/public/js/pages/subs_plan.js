jQuery(document).ready(function () {
    $('#add_new_subs').on('hidden.bs.modal', function (e) {
        $('#add_new_subs .modal-body').html('<div class="text-center"><div class="m-loader m-loader--primary m-loader--lg" style="width: 30px; display: inline-block;"></div></div>');
        $('#add_new_subs .modal-footer').html('');
    });
    $('.btn-purchase').click(function(e){
        $('#add_new_subs').modal('show');
        var dataPlan= $(this).attr('data-plan');
        $.ajax({
            type: 'get',
            url: 'subs-plan/dataplan',
            data: {
                'id': dataPlan
            },
            success:function(data){
                if (data.activeSubs===null){
                    bodyModal = "Subscribe to this Plan?";
                    footerModal =   '<button type="button" class="btn btn-secondary" data-dismiss="modal">\
                                        Close\
                                    </button>\
                                    <button type="button" data-cancel="none" data-plan ="'+ data.subs_plan + '" class="btn btn-primary btn-subscribe">\
                                        Subscribe\
                                    </button>';
                }
                else{
                    if (dataPlan != data.activeSubs.subs_plan_id){ 
                        bodyModal = "You are currently subscribed to <strong>" + data.activeName + " until "+ data.activeSubs.subs_end +
                                    "</strong><br> do you want to cancel the old subscription, or subscribe to this package after the old subscription end ?";
                        footerModal =   '<button type="button" class="btn btn-secondary" data-dismiss="modal">\
                                            Close\
                                        </button>\
                                        <button type="button" data-cancel="cancel-old" data-plan ="'+ data.subs_plan + '" class="btn btn-primary btn-subscribe">\
                                            Cancel Old Subs\
                                        </button>\
                                        <button type="button" data-cancel="continue-old" data-plan ="'+ data.subs_plan + '" class="btn btn-primary btn-subscribe">\
                                            Subscribe After Old Subs\
                                        </button>';
                    } else {
                        bodyModal = "Renew your Subscription: <strong>" + data.activeName + " ends in "+ data.activeSubs.subs_end + "</stong>";
                        footerModal = '<button type="button" class="btn btn-secondary" data-dismiss="modal">\
                                            Close\
                                        </button>\
                                        <button type="button" data-cancel="continue-old" data-plan ="'+ data.subs_plan + '" class="btn btn-primary btn-subscribe">\
                                            Renew Subscription\
                                        </button>';
                    }
                };
                $('#add_new_subs .modal-body').html(bodyModal);
                $('#add_new_subs .modal-footer').html(footerModal);
            },
            error: function(response){
                console.log(response);
                $('#add_new_subs').modal('hide');
            }
        });
    });

    $('#add_new_subs').on('click', '.btn-subscribe', function(e){
        $('#add_new_subs').modal('hide');
        $('#add_new_subs').on('hidden.bs.modal', function (e) {
            snap.show();
        });
        $.ajax({
            type: 'get',
            url: 'subs-plan/getToken',
            data: {
                'id': $(this).attr('data-plan'),
                'cancellation' : $(this).attr('data-cancel')
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
                snap.hide();
            }
            
        });
    });
});
