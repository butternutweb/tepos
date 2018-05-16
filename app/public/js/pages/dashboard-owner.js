var Dashboard = function () {
    var run = function(){
        $.ajax({
            type: 'get',
            url: 'dashb/dashdatas',
            success:function(data){
                DrawChart(data)
            },
            error:function(msg){
                console.log(msg)
            }
        });
    };
    var DrawChart = function (datas) {
        $('.total_sales .total_sales_desc').html('Out of '+datas.transactionSum+' transaction');
        $('.total_sales .total_sales_number').html('+Rp '+datas.transactionTotal);
        $('.profit .profit_desc').html('with '+datas.monthTransaction+' transaction');
        $('.profit .profit_number').html('Rp '+datas.monthProfit);
        $('.product_stats .product_stats_number').html(datas.productActive);
        var e=$("#m_chart_daily_sales");
        if (0 != e.length){
            var t = {
                labels : datas.labels,
                datasets: [{
                    label: "Sales in Rp",
                    backgroundColor: "#716aca",
                    data: datas.sales,
                    yAxisID: 'y-axis0'
                  }, {
                    label: "Product Sold",
                    backgroundColor: "#00c5dc",
                    data: datas.qty,
                    yAxisID: 'y-axis1'
                  }
                ]
            };
            new Chart(e,{
                type: 'bar',
                data: t,
                options: {
                    barValueSpacing: 5,
                    barRadius:4,
                    responsive:true,
                    maintainAspectRatio:false,
                    legend:{
                        display: false
                    },
                    tooltips : {
                        mode : 'index'
                    },
                    scales: {
                        xAxes:[{
                            display: false,
                            gridLines:false,
                            stacked: false

                        }],
                        yAxes:[
                            {
                                'id': "y-axis0",
                                gridLines: false,
                                stacked: false,
                                display: false,
                                ticks: {
                                    beginAtZero: true
                                }
                            },
                            {
                                'id': "y-axis1",
                                gridLines: false,
                                stacked: false,
                                display: false,
                                ticks: {
                                    beginAtZero: true
                                }
                            }
                        ]
                    }
                }
            });
        };
        var el=$("#m_chart_popular");
        if (0 != el.length){
            var t = {
                labels : datas.names,
                datasets: [{
                    backgroundColor: ["#AB5E71","#83779E","#3B909E","#479C71","#929B43","#D78B4E"],
                    data: datas.sortedProduct
                  }
                ]
            };
            new Chart(el,{
                type: 'doughnut',
                data: t,
                options: {
                    responsive:true,
                    maintainAspectRatio:false,
                    cutoutPercentage:60,
                    legend:{
                        display: true,
                        position: "right",
                        labels:{
                            fontSize:7,
                            boxWidth:7
                        }
                    }
                }
            });
        };
    };

    return {
        init: function () {
            run();
        },
    };
}();

jQuery(document).ready(function () {
    Dashboard.init();
});