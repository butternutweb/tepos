var Datatable = function () {
    
    var runCost = function () {

        var store_id = $("#store-id").html();

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/store/' + store_id  + '/cost/ajax',
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
                input: $('#m_search_cost')
            },

            columns: [{
                field: "id",
                title: "#",
                width: 20,
                sortable: false,
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            }, {
                field: "name",
                title: "Name",
                width: 200,
            }, {
                field: "amount",
                title: "Amount",
                width: 100,
                sortable: false,
                template: function (row) {
                    return 'Rp' + row.amount + ',-';
                }
            }, {
                field: "date",
                title: "Date",
                width: 100,
                sortable: false,
                template: function (row) {
                    return row.date.split(' ')[0];
                }
            }, {
                field: "Actions",
                title: "Actions",
                width: 110,
                sortable: false,
                overflow: 'visible',
                template: function (row) {
                    return '\
                        <a href="/store/' + store_id + '/cost/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_cost_modal" onclick="Datatable.deleteCostModal(' + store_id + ', ' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_cost').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('.m_datatable#m_datatable_cost')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_cost').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_cost').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_cost').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_cost').collapse('hide');
                }
            });

        $('#m_bulk_delete_cost').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/store/' + store_id + '/cost/bulk-delete',
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

    var runStaff = function () {

        var store_id = $("#store-id").html();
        
        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/store/' + store_id + '/staff/ajax',
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
                input: $('#m_search_staff')
            },

            columns: [{
                field: "id",
                title: "#",
                width: 20,
                sortable: false,
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            }, {
                field: "username",
                title: "Username",
                width: 200,
            }, {
                field: "email",
                title: "Email",
                width: 200,
            }, {
                field: "name",
                title: "Name",
                width: 200,
            }, {
                field: "phone",
                title: "Phone",
                width: 200,
            }, {
                field: "salary",
                title: "Salary",
                width: 200,
                template: function (row) {
                    if (row.salary) {
                        return 'Rp' + row.salary + ',-';
                    }
                    return '-';
                }
            }, {
                field: "status",
                title: "Status",
                width: 200,
                sortable: false,
                template: function (row) {
                    var status = {
                        'Active': {'class': ' m-badge--success'},
                        'Inactive': {'class': ' m-badge--danger'}
                    };
                    return '<span class="m-badge ' + status[row.status].class + ' m-badge--wide">' + row.status + '</span>';
                }
            }, {
                field: "Actions",
                title: "Actions",
                width: 110,
                sortable: false,
                overflow: 'visible',
                template: function (row) {
                    return '\
                        <a href="/store/' + store_id + '/staff/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_staff_modal" onclick="Datatable.deleteStaffModal(' + store_id + ', ' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_staff').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('#m_form_staff_status').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.status_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.status_id !== 'undefined' ? query.status_id : '');

        $('#m_form_staff_status').selectpicker();

        $('.m_datatable#m_datatable_staff')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_staff').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_staff').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_staff').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_staff').collapse('hide');
                }
            });

        $('#m_bulk_delete_staff').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/store/' + store_id + '/staff/bulk-delete',
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

    var runStoreProduct = function () {

        var store_id = $("#store-id").html();

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/store/' + store_id + '/product/ajax',
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
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
                input: $('#m_search_store_product')
            },

            columns: [{
                field: "id",
                title: "#",
                width: 20,
                sortable: false,
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            }, {
                field: "name",
                title: "Name",
                width: 200,
                template: function (row) {
                    return '<a class="m-link" href="/product/' + row.product_id + '">' + row.product + '</a>';
                }
            }, {
                field: "selling_price",
                title: "Selling Price",
                width: 200,
                template: function (row) {
                    return 'Rp' + row.selling_price + ',-';
                }
            }, {
                field: "Actions",
                title: "Actions",
                width: 110,
                sortable: false,
                overflow: 'visible',
                template: function (row) {
                    return '\
                        <a href="/store/' + store_id + '/product/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_store_product_modal" onclick="Datatable.deleteStoreProductModal(' + store_id + ', ' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_store_product').mDatatable(options);

        $('.m_datatable#m_datatable_store_product')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_store_product').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_store_product').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_store_product').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_store_product').collapse('hide');
                }
            });

        $('#m_bulk_delete_store_product').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/store/' + store_id + '/product/bulk-delete',
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
            runCost();
            runStaff();
            runStoreProduct();
        },
        deleteCostModal: function(store_id, id) {
            $("#m_delete_cost").attr("action", "/store/" + store_id + "/cost/" + id);
        },
        deleteStaffModal: function(store_id, id) {
            $("#m_delete_staff").attr("action", "/store/" + store_id + "/staff/" + id);
        },
        deleteStoreProductModal: function(store_id, id) {
            $("#m_delete_store_product").attr("action", "/store/" + store_id + "/product/" + id);
        }
    };
}();

jQuery(document).ready(function () {
    Datatable.init();
});