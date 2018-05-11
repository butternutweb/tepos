var Datatable = function () {
    
    var run = function () {

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/transaction/ajax',
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
                input: $('#m_search_transaction')
            },

            columns: [{
                field: "id",
                title: "#",
                width: 20,
                sortable: false,
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            }, {
                field: "invoice",
                title: "Invoice",
                width: 200,
            }, {
                field: "date",
                title: "Date",
                width: 200,
                template: function (row) {
                    return row.date.split(":")[0] + ':' + row.date.split(":")[1];
                }
            }, {
                field: "note",
                title: "Note",
                width: 200,
                template: function (row) {
                    if (row.note) {
                        return row.note;
                    }
                    return '-';
                }
            }, {
                field: "staff",
                title: "Staff",
                width: 200,
                template: function (row) {
                    return '<a class="m-link" href="/store/' + row.store_id + '/staff/' + row.staff_id + '">' + row.staff + ' (' + row.store + ')' + '</a>';
                }
            }, {
                field: "status",
                title: "Status",
                width: 200,
                template: function (row) {
                    var status = {
                        'Completed': {'class': ' m-badge--success'},
                        'On Progress': {'class': ' m-badge--danger'}
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
                        <a href="/transaction/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_transaction_modal" onclick="Datatable.deleteTransactionModal(' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_transaction').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('#m_form_transaction_status').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.status_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.status_id !== 'undefined' ? query.status_id : '');

        $('#m_form_transaction_staff').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.staff_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.staff_id !== 'undefined' ? query.staff_id : '');

        $('#m_form_transaction_status, #m_form_transaction_staff').selectpicker();

        $('.m_datatable#m_datatable_transaction')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_transaction').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_transaction').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_transaction').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_transaction').collapse('hide');
                }
            });

        $('#m_bulk_delete_transaction').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/transaction/bulk-delete',
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
        deleteTransactionModal: function(id) {
            $("#m_delete_transaction").attr("action", "/transaction/" + id);
        }
    };
}();

jQuery(document).ready(function () {
    Datatable.init();
});