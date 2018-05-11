var Datatable = function () {
    
    var run = function () {

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/product/ajax',
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
                input: $('#m_search_product')
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
                field: "sku",
                title: "SKU",
                width: 200,
                template: function (row) {
                    if (row.sku) {
                        return row.sku;
                    }
                    return '-';
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
                field: "capital_price",
                title: "Capital Price",
                width: 200,
                template: function (row) {
                    if (row.capital_price) {
                        return 'Rp' + row.capital_price + ',-';
                    }
                    return '-';
                }
            }, {
                field: "sub_category",
                title: "Sub Category",
                width: 200,
                sortable: false,
                template: function (row) {
                    return '<a class="m-link" href="/category/' + row.category_id + '/sub-category/' + row.sub_category_id + '">' + row.sub_category + '</a>';
                }
            }, {
                field: "Actions",
                title: "Actions",
                width: 110,
                sortable: false,
                overflow: 'visible',
                template: function (row) {
                    return '\
                        <a href="/product/' + row.id + '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
                            <i class="la la-edit"></i>\
                        </a>\
                        <button type="button" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#m_delete_product_modal" onclick="Datatable.deleteProductModal(' + row.id + ')">\
                            <i class="la la-trash"></i>\
                        </button>\
                    ';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_product').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('#m_form_product_sub_category').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.sub_category_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.sub_category_id !== 'undefined' ? query.sub_category_id : '');

        $('#m_form_product_sub_category').selectpicker();

        $('.m_datatable#m_datatable_product')
            .on('m-datatable--on-check', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_product').html(count);
                if (count > 0) {
                    $('#m_datatable_group_action_form_product').collapse('show');
                }
            })
            .on('m-datatable--on-uncheck m-datatable--on-layout-updated', function (e, args) {
                var count = datatable.getSelectedRecords().length;
                $('#m_datatable_selected_number_product').html(count);
                if (count === 0) {
                    $('#m_datatable_group_action_form_product').collapse('hide');
                }
            });

        $('#m_bulk_delete_product').click(function() {
            var datas = [];
            $('.m-checkbox--solid.m-checkbox--brand').each(function() {
                if (!$(this).hasClass('m-checkbox--all') && $(this).find('input[type=checkbox]').is(':checked')) {
                    datas.push($(this).find('input[type=checkbox]').val());
                }
            });

            $.ajax({
                type: 'post',
                url: '/product/bulk-delete',
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
        deleteProductModal: function(id) {
            $("#m_delete_product").attr("action", "/product/" + id);
        }
    };
}();

jQuery(document).ready(function () {
    Datatable.init();
});