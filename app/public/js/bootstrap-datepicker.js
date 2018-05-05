var BootstrapDatepicker = function () {
    
    var run = function () {
        $('#date').datepicker({
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: "yyyy-mm-dd",
        });

        $('#datetime').datetimepicker({
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: "yyyy-mm-dd hh:ii",
        });
    }

    return {
        init: function() {
            run(); 
        }
    };
}();

jQuery(document).ready(function() {    
    BootstrapDatepicker.init();
});