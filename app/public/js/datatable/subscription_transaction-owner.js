var Datatable = function () {
    
    var run = function () {

        var options = {
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'http://localhost:8000/subs-trans/ajax',
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
                field: "date",
                title: "Date",
                width: 200,
                template: function (row) {
                    return row.date.split(" ")[0];
                }
            }, {
                field: "payment_method",
                title: "Payment Method",
                width: 200,
            }, {
                field: "subscription_plan",
                title: "Subscription Plan",
                width: 200,
                template: function (row) {
                    return '<a class="m-link" href="/subs-plan/' + row.subscription_plan_id + '">' + row.subscription_plan + '</a>';
                }
            }]
        };

        var datatable = $('.m_datatable#m_datatable_subscription_transaction').mDatatable(options);

        var query = datatable.getDataSourceQuery();

        $('#m_form_subscription_transaction_subscription_plan').on('change', function () {
            var query = datatable.getDataSourceQuery();
            query.subscription_plan_id = $(this).val();
            datatable.setDataSourceQuery(query);
            datatable.load();
        }).val(typeof query.subscription_plan_id !== 'undefined' ? query.subscription_plan_id : '');

        $('#m_form_subscription_transaction_subscription_plan').selectpicker();
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