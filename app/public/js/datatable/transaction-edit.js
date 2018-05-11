var Datatable = function () {
    
    var run = function () {

        var transaction_id = $("#transaction-id").html();

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/transaction/' + transaction_id + '/product/ajax',
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
                input: $('#m_search_transaction_product')
            },

            columns: [{
                field: "id",
                title: "#",
                width: 20,
                sortable: false,
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            }, {
                field: "product",
                title: "Product",
                width: 200,
                template: function (row) {
                    return '<a class="m-link" href="/product/' + row.product_id + '">' + row.product + '</a>';
                }
            }, {
                field: "qty",
                title: "Quantity",
                width: 200,
            }, {
                field: "price",
                title: "Price",
                width: 200,
                template: function (row) {
                    return 'Rp' + row.price + ',-';
                }
            }, {
                field: "sub_total",
                title: "Sub Total",
                width: 200,
                template: function (row) {
                    return 'Rp' + (parseInt(row.qty) * parseInt(row.price)) + ',-';
                }
            }, {
                field: "Actions",
                title: "Actions",
                width: 110,
                sortable: false,
                overflow: 'visible',
                template: function (row) {
                    return '\
                        <a href="/transaction/' + transaction_id + '/product/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_transaction_product_modal" onclick="Datatable.deleteTransactionProductModal(' + transaction_id + ', ' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_transaction_product').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('.m_datatable#m_datatable_transaction_product')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_transaction_product').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_transaction_product').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_transaction_product').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_transaction_product').collapse('hide');
                }
            });

        $('#m_bulk_delete_transaction_product').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/transaction/' + transaction_id + '/product/bulk-delete',
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
        deleteTransactionProductModal: function(transaction_id, id) {
            $("#m_delete_transaction_product").attr("action", "/transaction/" + transaction_id + "/product/" + id);
        }
    };
}();

jQuery(document).ready(function () {
    Datatable.init();
});