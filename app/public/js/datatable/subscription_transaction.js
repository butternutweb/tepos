var Datatable = function () {
    
    var run = function () {

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/subs-trans/ajax',
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }
                },
                
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },            

            layout: {
                theme: 'default',
                scroll: true,
                height: 550,
                footer: false
            },

            sortable: true,
            pagination: true,
            
            toolbar: {
                items: {
                    pagination: {
                        pageSizeSelect: [10, 20, 30, 50, 100]
                    }
                }
            },

            search: {
                input: $('#m_search_subscription_transaction')
            },

            columns: [{
                field: "id",
                title: "#",
                width: 20,
                sortable: false,
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            }, {
                field: "date",
                title: "Date",
                width: 200,
                template: function (row) {
                    return row.date.split(" ")[0];
                }
            }, {
                field: "subs_end",
                title: "Subscription End",
                width: 200,
                template: function (row) {
                    return row.subs_end.split(" ")[0];
                }
            }, {
                field: "payment_method",
                title: "Payment Method",
                width: 200,
            }, {
                field: "payment_status",
                title: "Payment Status",
                width: 200,
            }, {
                field: "owner",
                title: "Owner",
                width: 200,
                template: function (row) {
                    return '<a class="m-link" href="/owner/' + row.owner_id + '">' + row.owner + '</a>';
                }
            }, {
                field: "subscription_plan",
                title: "Subscription Plan",
                width: 200,
                template: function (row) {
                    return '<a class="m-link" href="/subs-plan/' + row.subscription_plan_id + '">' + row.subscription_plan + '</a>';
                }
            }, {
                field: "Actions",
                title: "Actions",
                width: 110,
                sortable: false,
                overflow: 'visible',
                template: function (row) {
                    return '\
                        <a href="/subs-trans/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_subscription_transaction_modal" onclick="Datatable.deleteSubscriptionTransactionModal(' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_subscription_transaction').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('#m_form_subscription_transaction_owner').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.owner_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.owner_id !== 'undefined' ? query.owner_id : '');

        $('#m_form_subscription_transaction_subscription_plan').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.subscription_plan_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.subscription_plan_id !== 'undefined' ? query.subscription_plan_id : '');

        $('#m_form_subscription_transaction_owner, #m_form_subscription_transaction_subscription_plan').selectpicker();

        $('.m_datatable#m_datatable_subscription_transaction')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_subscription_transaction').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_subscription_transaction').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_subscription_transaction').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_subscription_transaction').collapse('hide');
                }
            });

        $('#m_bulk_delete_subscription_transaction').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/subs-trans/bulk-delete',
                data: {
                    'ids': datas,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    datatable.reload();
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
        });
    };

    return {
        init: function () {
            run();
        },
        deleteSubscriptionTransactionModal: function(id) {
            $("#m_delete_subscription_transaction").attr("action", "/subs-trans/" + id);
        }
    };
}();

jQuery(document).ready(function () {
    Datatable.init();
});