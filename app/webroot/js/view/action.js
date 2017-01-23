// TODO:画像アップロード処理は依存が強すぎてgl_basic.jsに残したままなので、本ファイルに移行する

var Page = {
  el: "#ActionFormWrapper",
  conf: {
    kr_progress: "#SelectKrProgress",
    form: "#CommonActionDisplayForm",
    input_fields: ["key_result_current_value", 'name', 'key_result_id', 'goal_id']
  },
  submit_flg: false,
  init: function () {
    var self = this;
    // ゴール選択
    $(this.el).on("change", ".js-change-goal", function () {
      self.selectGoal(this);
    });
    // 進捗を更新するKR選択
    $(this.el).on("click", ".js-select-kr", function () {
      self.selectKr(this)
    });
    // KR進捗の入力フォーカスした際に外側の行のクリックイベントが反応しないようにする
    $(this.conf.kr_progress).on("click", "input", function (e) {
      e.stopPropagation();
    });
    // フォームサブミット
    $(this.conf.form).submit(function (e) {
      // アクション編集の場合submitさせる
      // TODO:将来的にAPI化
      if ($(this).data('is-edit')) {
        return true;
      }

      e.stopImmediatePropagation();
      e.preventDefault();
      if (self.submit_flg) {
        return false;
      }
      self.submit_flg = true;
      self.submit(this);
      // TODO:submit_flgを用いた処理削除
      // なぜかsubmitが二度呼ばれる問題があるので、やむなく以下処理にする
      setTimeout(function () {
        self.submit_flg = false;
        return true;
      }, 1000);
    });
  },
  submit: function (form) {
    var self = this;

    // 多重サブミット対策
    $(form).find('.js-action-submit-button').addClass("is-disabled");

    if (!checkUploadFileExpire(form.id)) {
      // 画像アップロード画面に戻す
      var $btn_add_img = $('#ActionImageAddButton');
      var target_ids = $btn_add_img.attr('target-id').split(',');

      for (var i = 0; i < target_ids.length; i++) {
        $('#' + target_ids[i]).hide();
      }
      $btn_add_img.show();
    }
    $(form).find(".changed").removeClass("changed");

    var form_data = $(form).serializeArray();
    var switch_el = $(self.el).find(".action-kr-progress-edit-item.is-active .js-kr-progress-check-complete");
    if (switch_el.size() > 0 && !switch_el.prop('checked')) {
      form_data.push({name: "data[ActionResult][key_result_current_value]", value: 0});
    }

    $.ajax({
      url: "/api/v1/actions",
      type: 'POST',
      data: form_data,
      success: function (data) {
        // 処理中に値が変更されたケースを想定して、入力途中の警告イベントを解除する
        $(window).off('beforeunload');
        location.href = "/";
      },
      error: function (res, textStatus, errorThrown) {
        var body = res.responseJSON;
        var message = body.message;
        var errHtml = "";

        // 多重サブミット対策を解除する
        $(form).find('.js-action-submit-button').removeClass("is-disabled");

        // 既にエラーメッセージが表示されてる場合はそれを非表示にする
        if ($('.action-form-errors').length) {
          $('.action-form-errors').remove();
        }

        // バリデーションエラー
        var errTemplate = '<div class="action-form-errors alert alert-danger mtb_8px ml_8px mr_8px">#error#</div>';
        if (res.status == 400 && body.validation_errors) {
          var errors = body.validation_errors;
          var errMsgs = [];
          for (var key in errors) {
            if (errors.hasOwnProperty(key)) {
              errMsgs.push(errors[key]);
            }
          }
          errHtml = errTemplate.replace(/#error#/g, errMsgs.join('<br>'));
          $(self.conf.form).prepend(errHtml);
          return false;
        }

        // 409
        if (res.status == 409) {
          self.selectGoal($(self.el).find('.js-change-goal'));
        }

        errHtml = errTemplate.replace(/#error#/g, message);
        $(self.conf.form).prepend(errHtml);
        return false;
      }
    });
  },
  selectGoal: function (el) {
    $(el).closest(".has-success").removeClass("has-success");
    var goal_id = $(el).val();

    attrUndefinedCheck(el, "ajax-url");
    var url = $(el).attr("ajax-url") + $(el).val();

    var self = this;
    $.get(url, function (data) {
      var $kr_progress = $($(self.el).find(self.conf.kr_progress));
      if (data.html) {
        $kr_progress.empty().append(data.html);
        $kr_progress.find(".js-kr-progress-check-complete").bootstrapSwitch("disabled", true);
        //key_result_idがcakeのurlパラメータに存在し、かつkrのlistに含まれる場合は選択済みにする
        var pre_selected_kr_id = cake.request_params.named.key_result_id;
        var $pre_selected_kr = $kr_progress.find("[data-kr-id='" + pre_selected_kr_id + "']");
        if ($pre_selected_kr.size()) {
          $pre_selected_kr.trigger('click');
        } else {
          $kr_progress.find('.js-select-kr:first-child').trigger('click');
        }
      } else {
        $kr_progress.empty();
      }
    });
  },
  selectKr: function (el) {
    var selected = $(el).data("selected");
    if (selected) {
      // this.deselectKrProgressInActionForm($(el));
    } else {
      // KRの選択
      var $kr_progress = $($(this.el).find(this.conf.kr_progress));
      var activeKr = $kr_progress.find(".action-kr-progress-edit-item.is-active");
      this.deselectKrProgressInActionForm($(activeKr));
      this.selectKrProgressInActionForm($(el));
    }
  },
  selectKrProgressInActionForm: function ($el) {
    var activeClass = "is-active";
    var base = "action-kr-progress-edit-item";
    // KRの選択
    $el.addClass(activeClass);
    $el.find(".js-show-input-kr-progress").show();
    $el.find(".action-kr-progress-edit-item, .action-kr-progress-edit-item-box, .action-kr-progress-edit-item-title").addClass(activeClass);
    var check_circle = $el.find(".action-kr-progress-edit-item-check-circle");
    $(check_circle).addClass(activeClass);
    $(check_circle).append('<i class="fa fa-check action-kr-progress-edit-item-check-circle-inner"></i>');
    $el.find("input").prop("disabled", false);
    $el.find(".js-kr-progress-check-complete").bootstrapSwitch("disabled", false);
    $el.data("selected", 1);
  },
  deselectKrProgressInActionForm: function ($el) {
    var activeClass = "is-active";
    $el.removeClass(activeClass);
    $el.find(".js-show-input-kr-progress").hide();
    $el.find(".action-kr-progress-edit-item, .action-kr-progress-edit-item-box, .action-kr-progress-edit-item-title")
      .removeClass(activeClass);
    var check_circle = $el.find(".action-kr-progress-edit-item-check-circle");
    $(check_circle).removeClass(activeClass).empty();
    $el.find("input").prop("disabled", true);
    $el.find(".js-kr-progress-check-complete").bootstrapSwitch("disabled", true);
    $el.data("selected", 0);
  }
};
jQuery(function ($) {
  Page.init();
});
