/**
 * Created by daikihirakata on 2014/05/23.
 */
(function($) {
    /**
     * アルファベットのみ
     * @type {{validate: validate}}
     */
    $.fn.bootstrapValidator.validators.alphaOnly = {
        validate: function(validator, $field, options) {
            var value = $field.val();
            if (value == '') {
                return true;
            }
            return /^[a-zA-Z]+$/.test(value);
        }
    }
}(window.jQuery));
