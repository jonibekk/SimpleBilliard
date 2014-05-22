/**
 * Created by bigplants on 5/22/14.
 */
(function () {
    jQuery.validator.addMethod("alpha", function (value, element, params) {
        return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
    }, jQuery.validator.format("Alpha Only"));
}());