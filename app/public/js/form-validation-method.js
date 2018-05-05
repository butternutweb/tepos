$(document).ready(function () {
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
    }, "Letters, numbers, and underscores only please");
});