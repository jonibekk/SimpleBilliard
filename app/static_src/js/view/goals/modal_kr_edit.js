;(function ($) {
  var self = this;
  var short_units = {};
  var $modal;
  var current_unit;
  var current_start_value;
  var kr_id;
  var form;

  $.fn.modalKrEdit = function (options) {
    init(options);
    return this;
  }

  /**
   * 初期化
   * @param options
   */
  function init(options) {
    kr_id = options.kr_id;
    //noinspection JSUnresolvedVariable
    var url = "/goals/ajax_get_edit_key_result_modal/key_result_id:" + kr_id;

    // TODO:他にならってこのセレクタ指定をしているが、別の方法を検討
    $modal = $('<div class="modal on fade" tabindex="-1"></div>');

    // $(form).unbind("submit");
    $.get(url, function (data) {
      $modal.append(data);
      modalFormCommonBindEvent($modal);
      form = $modal.find("#KrEditForm");
      // フォームサブミット
      $(form).on('submit', submit);


      $modal.on('shown.bs.modal', showInitModal);
      var $select_unit = $($modal.find('.js-select-value-unit'));
      short_units = $select_unit.data('short_units');
      current_unit = $select_unit.val();
      $modal.find('.js-display-short-unit').html(short_units[current_unit]);

      current_start_value = $($modal.find('.js-start-value')).val();

      $modal.on('change', '.js-select-value-unit', changeUnit);
      $modal.on('hidden.bs.modal', function (e) {
        $(self).empty();
      });
      // TODO:APIのエラーメッセージ表示と衝突するため一時的にコメントアウト。将来的にどうするか検討
      // $modal.find('form').bootstrapValidator(getValidatorOptions());
      $modal.modal();
      $('body').addClass('modal-open');
    });

  }

  /**
   * バリデーション設定取得
   * @returns object
   */
  function getValidatorOptions() {
    return {
      live: 'enabled',

      fields: {
        "data[KeyResult][start_date]": {
          validators: {
            callback: {
              message: cake.message.notice.e,
              callback: function (value, validator) {
                var m = new moment(value, 'YYYY/MM/DD', true);
                return m.isBefore($('[name="data[KeyResult][end_date]"]').val());
              }
            },
            date: {
              format: 'YYYY/MM/DD',
              message: cake.message.validate.date_format
            }
          }
        },
        "data[KeyResult][end_date]": {
          validators: {
            callback: {
              message: cake.message.notice.f,
              callback: function (value, validator) {
                var m = new moment(value, 'YYYY/MM/DD', true);
                return m.isAfter($('[name="data[KeyResult][start_date]"]').val());
              }
            },
            date: {
              format: 'YYYY/MM/DD',
              message: cake.message.validate.date_format
            }
          }
        }
      }
    };
  }

  /**
   * モーダル表示時処理
   * @param e
   */
  function showInitModal(e) {
    var lang = 'en';
    if (cake.lang === 'ja' || cake.lang === 'jpn') {
      lang = 'ja';
    }
    $modal.find('.input-group.date').datepicker({
      format: "yyyy/mm/dd",
      todayBtn: 'linked',
      language: lang,
      autoclose: true,
      todayHighlight: true
      //endDate:"2015/11/30"
    })
      .on('hide', function (e) {
        $(form).bootstrapValidator('revalidateField', "data[KeyResult][start_date]");
        $(form).bootstrapValidator('revalidateField', "data[KeyResult][end_date]");
      });
  }

  /**
   * 進捗単位変更イベント処理
   * @param e
   */
  function changeUnit(e) {
    var selected_unit = e.target.value;
    $modal.find('.js-display-short-unit').html(short_units[selected_unit]);

    /* 元の単位から変更した場合、注意メッセージ表示 */
    var warning_unit_change = $modal.find('.js-show-warning-unit-change').show();
    var $start_value = $($modal.find('.js-start-value'));
    if (current_unit != selected_unit) {
      warning_unit_change.show();
      $start_value.prop('disabled', false);
    } else {
      warning_unit_change.hide();
      $start_value.val(current_start_value);
      $start_value.prop('disabled', true);
    }
    /* 単位が「完了/未完了」の場合、開始/現在/目標値を非表示にする */
    var no_value = 2;
    var unit_values = $modal.find('.js-unit-values');
    if (selected_unit == no_value) {
      unit_values.hide();
    } else {
      unit_values.show();
    }
  }

  /**
   * 更新処理
   * @param e
   */
  function submit(e) {
    e.preventDefault;
    e.stopImmediatePropagation();

    var $select_unit = $($(this).find('.js-select-value-unit'));
    var input_unit = $select_unit.val();
    var confirm_msg = current_unit == input_unit ? cake.translation["Would you like to save?"] : cake.translation["All progress of this KR will be reset, is it really OK?"];
    if (!confirm(confirm_msg)) {
      /* キャンセルの時の処理 */
      return false;
    }
    var self = this;
    $(this).find(".changed").removeClass("changed");
    $(this).find('.js-validation-err').remove();

    var form_data = $(this).serializeArray();

    $.ajax({
      url: "/api/v1/key_results/"+kr_id,
      type: 'PUT',
      data: form_data,
      success: function (data) {
        new Noty({
        type: 'success',
        text: '<h4>'+cake.word.success+'</h4>'+cake.translation["Updated KR."],
      }).show();
        location.reload(true);
      },
      error: function (res, textStatus, errorThrown) {
        $modal.find('.js-validation-err').remove();
        $modal.find('.has-success').removeClass('has-success');
        var body = res.responseJSON;
        // バリデーションエラー
        var errTemplate = '<div class="has-error"><small class="js-validation-err help-block"  style="display: block;">#error#</small></div>';
        if (res.status == 400 && body.validation_errors) {
          var errors = body.validation_errors;
          for (var key in errors) {
            if (errors.hasOwnProperty(key)) {
              errHtml = errTemplate.replace(/#error#/g, errors[key]);
              if (key == "value_unit") {
                $modal.find('.js-progress-block').after(errHtml);
              } else {
                $modal.find('input[name="data[KeyResult]['+key+']"]').parent().after(errHtml);
              }
            }
          }
          return false;
        }

        var errHtml = '<div class="js-validation-err alert alert-danger mtb_8px ml_8px mr_8px">' + body.message  +'</div>';
        $(form).prepend(errHtml);
        return false;

      }
    });
    return false;
  }
})(jQuery);
