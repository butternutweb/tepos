var Datatable = function () {
    
    var run = function () {

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'http://localhost:8000/owner/ajax',
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
                input: $('#m_search_owner')
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
                        <a href="/owner/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_owner_modal" onclick="Datatable.deleteOwnerModal(' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_owner').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('#m_form_owner_status').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.status_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.status_id !== 'undefined' ? query.status_id : '');

        $('#m_form_owner_status').selectpicker();

        $('.m_datatable#m_datatable_owner')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_owner').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_owner').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_owner').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_owner').collapse('hide');
                }
            });

        $('#m_bulk_delete_owner').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/owner/bulk-delete',
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
        deleteOwnerModal: function(id) {
            $("#m_delete_owner").attr("action", "/owner/" + id);
        }
    };
}();

jQuery(document).ready(function () {
    Datatable.init();
});