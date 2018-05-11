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
                input: $('#m_search_checkout')
            },

            columns: [{
                field: "product",
                title: "Product",
                width: 200,
            }, {
                field: "price",
                title: "Price",
                width: 200,
                template: function (row) {
                    return 'Rp' + row.price;
                }
            }, {
                field: "qty",
                title: "Quantity",
                width: 200,
            }, {
                field: "sub_total",
                title: "Sub Total",
                width: 200,
                template: function (row) {
                    return 'Rp' + (parseInt(row.qty) * parseInt(row.price)) + ',-';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_checkout').mDatatable(options);
    };

    return {
        init: function () {
            run();
        },
    };
}();

jQuery(document).ready(function () {
    Datatable.init();
});