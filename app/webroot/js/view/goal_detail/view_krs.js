// TODO:gl_basic.jsにあるゴール詳細.KR一覧の処理をここに移行

var ViewKrs = {
  el: "#ViewKrs",
  init: function () {
    $(this.el).on("click", '.js-show-modal-edit-kr', this.showModalEditKr);
  },
  showModalEditKr: function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    if (url.indexOf('#') == 0) {
      $(url).modal('open');
    } else {
      var kr_id = $(this).data('kr_id');
      $(this).modalKrEdit({kr_id: kr_id});
    }
  }
};
jQuery(function ($) {
  ViewKrs.init();
});
