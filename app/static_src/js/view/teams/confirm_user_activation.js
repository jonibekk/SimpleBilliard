"use strict";

$(document).on("change", ".js-required-agreement", function() {
  var $submitButton = $('.js-activate-submit');
  if (this.checked) {
    $submitButton.attr('disabled', false);
  } else {
    $submitButton.attr('disabled', true);
  }
});
