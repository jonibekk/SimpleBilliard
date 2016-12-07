var Page = {
  el: "#ViewKrs",
  init: function () {
    var self = this;
    // ゴール選択
    $(self.el).on("click", ".js-show-detail-progress-value", self.showDetailProgress)
  },
  showDetailProgress: function () {
    var current_value = $(this).data('current_value');
    var start_value = $(this).data('start_value');
    var target_value = $(this).data('target_value');
    $(this).find('.goal-detail-kr-progress-text').text(current_value);
    $(this).find('.goal-detail-kr-progress-values-left').text(start_value);
    $(this).find('.goal-detail-kr-progress-values-right').text(target_value);
  },
};
jQuery(function ($) {
  // Page.init();
});
