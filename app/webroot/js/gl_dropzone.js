/**
 * Created by bigplants on 12/19/16.
 */
$(function () {
  /**
   * ファイルのドラッグ & ドロップ 設定
   *
   * 設定例）
   * HTML:
   *   <div id="DragDropArea">
   *      <form id="PostForm">
   *         <div id="PreviewArea></div>
   *         <!-- form の最後に data['file_id'][] の名前で hidden が追加される -->
   *      </form>
   *      <a href="#" id="UploadButton">ファイルを添付</a>
   *   </div>
   *   <?= $this->element('file_upload_form') ?>
   *
   * JavaScript:
   *   var postParams = {
     *     formID: 'PostForm',
     *     previewContainerID: 'PreviewArea'
     *   };
   *   var $uploadFileForm = $(document).data('uploadFileForm');
   *   $uploadFileForm.registerDragDropArea('#DragDropArea', postParams);
   *   $uploadFileForm.registerAttachFileButton('#UploadButton', postParams);
   *
   */
    // ファイルアップロード用フォーム
  var $uploadFileForm = $('#UploadFileForm');

// ファイル削除用フォーム
  var $removeFileForm = $('#RemoveFileForm');
// 手動ファイル添付用ボタン
  var $uploadFileAttachButton = $('#UploadFileAttachButton');
// プレビューエリアのテンプレート
  var previewTemplateDefault =
    '<div class="dz-preview dz-default-preview panel">' +
    '  <div class="dz-details">' +
    '    <a href="#" class="pull-right font_lightgray" data-dz-remove><i class="fa fa-times"></i></a>' +
    '    <div class="dz-thumb-container pull-left">' +
    '      <i class="fa fa-file-o file-other-icon"></i>' +
    '      <img class="dz-thumb none" data-dz-thumbnail /></div>' +
    '    <span class="dz-name font_14px font_bold font_verydark pull-left" data-dz-name></span><br>' +
    '    <span class="dz-size font_11px font_lightgray pull-left" data-dz-size></span>' +
    '  </div>' +
    '  <div class="dz-progress progress">' +
    '    <div class="progress-bar progress-bar-info" role="progressbar"  data-dz-uploadprogress></div>' +
    '  </div>' +
    '</div>';

// アクションのメイン画像表示部分のテンプレート
  var previewTemplateActionImage =
    '<div class="dz-preview dz-action-photo-preview action-photo-preview upload-file-attach-button">' +
    '  <div class="dz-action-photo-details">' +
    '    <div class="dz-action-photo-thumb-container pull-left"><img class="dz-action-photo-thumb" data-dz-thumbnail /></div>' +
    '  </div>' +
    '  <div class="dz-action-photo-progress progress">' +
    '    <div class="progress-bar progress-bar-info" role="progressbar"  data-dz-uploadprogress></div>' +
    '  </div>' +
    '</div>';

  Dropzone.autoDiscover = false;
  Dropzone.options.UploadFileForm = {
    paramName: "file",
    maxFiles: 10,
    maxFilesize: cake.attachable_max_file_size_mb, // MB
    url: cake.url.upload_file,
    addRemoveLink: true,
    dictFileTooBig: cake.message.validate.dropzone_file_too_big,
    dictInvalidFileType: cake.message.validate.dropzone_invalid_file_type,
    dictMaxFilesExceeded: cake.message.validate.dropzone_max_files_exceeded,
    dictResponseError: cake.message.validate.dropzone_response_error,
    dictCancelUpload: cake.message.validate.dropzone_cancel_upload,
    dictCancelUploadConfirmation: cake.message.validate.dropzone_cancel_upload_confirmation,
    clickable: '#' + sanitize($uploadFileAttachButton.attr('id')),
    previewTemplate: previewTemplateDefault,
    thumbnailWidth: null,
    thumbnailHeight: 240,
    // ファイルがドロップされた時の処理
    drop: function (e) {
      $uploadFileForm.hide();
    },
    // ファイルがドロップされた後
    // Dropzone で受け付けるファイルだった時
    addedfile: function (file) {
      // previewContainer をドロップエリアに応じて入れ替える
      this.previewsContainer = $('#' + $uploadFileForm._params.previewContainerID).get(0);

      // コールバック関数実行 (beforeAddedFile)
      $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].beforeAddedFile.call(this, file);

      // Dropzone デフォルトの処理を実行
      this.defaultOptions.addedfile.call(this, file);
    },
    // ファイルがドロップされた後
    accept: function (file, done) {
      // コールバック関数実行 (beforeAccept)
      $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].beforeAccept.call(this, file);

      done();

      // コールバック関数実行 (afterAccept)
      $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].afterAccept.call(this, file);
    },
    // ファイル送信前
    sending: function (file, xhr, formData) {
      // コールバック関数実行 (beforeSending)
      $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].beforeSending.call(this, file, xhr, formData);
    },
    // 全てのファイルのアップロードが完了した後
    queuecomplete: function () {
      // コールバック関数実行 (afterQueueComplete)
      $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].afterQueueComplete.call(this);
    },
    // ファイルアップロード完了時
    success: function (file, res) {
      var $preview = $(file.previewTemplate);
      // エラー
      if (res.error) {
        $preview.remove();
        PNotify.removeAll();
        new PNotify({
          type: 'error',
          title: cake.message.notice.d,
          text: res.msg,
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
        });
        return;
      }

      // 処理成功
      // submit するフォームに hidden でファイルID追加
      var $form = $('#' + $uploadFileForm._params.formID);
      $form.append(
        $('<input type=hidden name=data[file_id][]>')
          .val(res.id)
          .attr('id', res.id)
          .attr('data-uploaded', Math.floor(new Date().getTime() / 1000)));

      // プレビューエリアをファイルオブジェクトにファイルIDを紐付ける
      $preview.data('file_id', res.id);
      file.file_id = res.id;

      // プログレスバー消す
      // 一瞬で消えるのを防止するため１秒待つ
      setTimeout(function () {
        $preview.find('.progress').css('visibility', 'hidden');
      }, 1000);

      // コールバック関数（afterSuccess）
      $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].afterSuccess.call(this, file);
    },
    // ファイル削除ボタン押下時
    removedfile: function (file) {
      var $preview = $(file.previewTemplate);

      // ファイルリストの参照が入れ替わっているので、フォーム別のデータを更新する
      $uploadFileForm._files[$uploadFileForm._params.formID] = Dropzone.instances[0].files;

      // キャンセルされたファイルの場合は処理しない
      if (file.status == Dropzone.CANCELED) {
        return;
      }

      // 既にDBに保存済のデータの場合（投稿編集時）
      if (file.saved_file) {
        // フォームの hidden を削除
        $('#AttachedFile_' + $preview.data('file_id')).remove();

        // 削除済ファイルの hidden を追加
        var $form = $('#' + $uploadFileForm._params.formID);
        $form.append($('<input type=hidden name=data[deleted_file_id][]>').val($preview.data('file_id')));

        // プレビューエリア削除
        $preview.fadeOut();
      }
      // 新しくアップロードするファイルの場合
      else {
        // キューに入ってるアップロードをキャンセルしようとした場合
        //   (アップロード中のキャンセルはcanceledコールバックが呼ばれるっぽい。
        //   このブロックはその前段階のキャンセル時の処理。)
        if ($preview.data('file_id') === undefined) {
          // アップロード中のキャンセル時は確認をはさむので、
          // ここでもそれに合わせて確認をはさむようにする
          if (!confirm(cake.message.validate.dropzone_cancel_upload_confirmation)) {
            return;
          }

          // キャンセルを確認出来るようにファイルの名前を強調して少しの間表示しておく
          $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.cancel + ')');
          setTimeout(function () {
            $preview.remove();
          }, 4000);
          $uploadFileForm.hide();
          PNotify.removeAll();

          // 成功
          new PNotify({
            type: 'success',
            title: cake.word.success,
            text: cake.message.validate.dropzone_cancel_upload,
            icon: "fa fa-check-circle",
            delay: 4000,
            mouse_reset: false
          });
          return;
        }
        $removeFileForm.find('input[name="data[AttachedFile][file_id]"]').val($preview.data('file_id'));
        $.ajax({
          url: cake.url.remove_file,
          type: 'POST',
          dataType: 'json',
          processData: false,
          data: $removeFileForm.serialize()
        })
          .done(function (res) {
            PNotify.removeAll();
            // エラー
            if (res.error) {
              new PNotify({
                type: 'error',
                title: cake.message.notice.d,
                text: res.msg,
                icon: "fa fa-check-circle",
                delay: 4000,
                mouse_reset: false
              });
              return;
            }

            // 成功
            new PNotify({
              type: 'success',
              title: cake.word.success,
              text: res.msg,
              icon: "fa fa-check-circle",
              delay: 2000,
              mouse_reset: false
            });
            // ファイルIDの hidden 削除
            $('#' + $preview.data('file_id')).remove();

            $preview.fadeOut('fast', function () {
              // コールバック関数実行 (afterRemoveFile)
              var previewContainerID = $preview.parent().attr('id');
              $uploadFileForm._callbacks[previewContainerID].afterRemoveFile.call(this, file);
            });
          })
          .fail(function (res) {
            PNotify.removeAll();
            new PNotify({
              type: 'error',
              title: cake.message.notice.d,
              text: cake.message.notice.d,
              icon: "fa fa-check-circle",
              delay: 4000,
              mouse_reset: false
            });
          });
      }
    },
    // アップロードがキャンセルされたとき
    // (キューにある状態の場合はremovedfile()が呼ばれる。ここは呼ばれない)
    canceled: function (file) {
      var $preview = $(file.previewTemplate);
      // キャンセルを確認出来るようにファイルの名前を強調して少しの間表示しておく
      $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.cancel + ')');
      setTimeout(function () {
        $preview.remove();
      }, 4000);
      $uploadFileForm.hide();
      PNotify.removeAll();
      new PNotify({
        type: 'success',
        title: cake.word.success,
        text: cake.message.validate.dropzone_cancel_upload,
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
      });
    },
    // サムネイル
    thumbnail: function (file, dataUrl) {
      var orientation = 0;
      EXIF.getData(file, function () {
        switch (parseInt(EXIF.getTag(file, "Orientation"))) {
          case 3:
            orientation = 180;
            break;
          case 6:
            orientation = -90;
            break;
          case 8:
            orientation = 90;
            break;
        }
        var thumbnailElement, _i, _ref;
        if (orientation != 0) {
          orientation = orientation + 180;
        }
        var $container = $(file.previewTemplate).find('.dz-thumb-container');
        if (file.type.match(/image/)) {
          $container.find('.fa').hide();
          $container.find('.dz-thumb').show();
        }
        _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (_i = 0; _i < _ref.length; _i++) {
          thumbnailElement = _ref[_i];
        }
        thumbnailElement.alt = file.name;
        thumbnailElement.src = dataUrl;
        thumbnailElement.id = "exif";
        var styles = {
          "transform": "rotate(" + orientation + "deg)",
          "-ms-transform": "rotate(" + orientation + "deg)",
          "-webkit-transform": "rotate(" + orientation + "deg)"
        };
        $("#exif").css(styles);
        $("#exif").removeAttr("id");
      });
    },
    // ファイルアップロード失敗
    error: function (file, errorMessage) {
      var $preview = $(file.previewTemplate);
      // エラーと確認出来るように失敗したファイルの名前を強調して少しの間表示しておく
      $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.error + ')');
      setTimeout(function () {
        $preview.remove();
      }, 4000);
      $uploadFileForm.hide();
      PNotify.removeAll();
      new PNotify({
        type: 'error',
        title: cake.message.notice.d,
        text: errorMessage,
        icon: "fa fa-check-circle",
        delay: 8000,
        mouse_reset: false
      });
    }
  };

// パラメータ
  $uploadFileForm._params = {};
// コールバック関数
  $uploadFileForm._callbacks = {};
// Dropzone のデフォルト設定
  $uploadFileForm._dzDefaultOptions = {};
// 仮アップロード中のファイル（アップロードフォームごとに保持する）
  $uploadFileForm._files = {};

// 登録済ドロップエリアとアップロードボタン
  $uploadFileForm._dragDropArea = {};
  $uploadFileForm._attachFileButton = {};

  /**
   * ドラッグ＆ドロップ対象エリアを設定する
   *
   * selector : string  (* 必須) ドロップエリアにする要素のセレクタ
   * params: object {
     *   formID: string|function  (* 必須) アップロードしたファイルIDを hidden で追加するフォームのID
     *   previewContainerID: string|function  (* 必須) プレビューを表示するコンテナ要素のID
     *   disableMultiple: 複数ファイル同時アップロードを無効にする（iphone で 「画像選択」と「写真を撮る」を選択出来るようにする）
     *   beforeAddedFile: function  コールバック関数
     *   beforeAccept: function   コールバック関数
     *   afterAccept: function  コールバック関数
     *   afterRemoveFile: function  コールバック関数
     *   afterSuccess: function   コールバック関数
     *   beforeSending: function コールバック関数
     *   afterQueueComplete: function コールバック関数
     * }
   * dzOptions: object {
     *    ...   Dropzone のオプション（デフォルトの設定を上書きする場合に指定）
     * }
   */
  $uploadFileForm.registerDragDropArea = function (selector, params, dzOptions) {
    if ($uploadFileForm._dragDropArea[selector]) {
      return true;
    }
    $uploadFileForm._dragDropArea[selector] = {
      selector: selector,
      params: params,
      dzOptions: dzOptions
    };

    $(document).on('dragover', selector, function (e) {
      e.preventDefault();
      $uploadFileForm._setParams(this, params, dzOptions);

      // ファイルアップロード用フォームのサイズと位置を合わせて重ねて表示させる
      var $dropArea = $(this);
      var pos = $dropArea.position();
      $uploadFileForm.appendTo($dropArea).css({
        width: $dropArea.outerWidth(),
        height: $dropArea.outerHeight(),
        paddingTop: $dropArea.outerHeight() / 2 - 18,
        top: pos.top,
        left: pos.left,
        position: 'absolute'
      }).addClass('drag-over').show().find('.upload-file-form-content').show();
    });
  };

  /**
   * ファイル添付用ボタンを登録する
   *
   * 引数は registerDragDropArea と同じ
   */
  $uploadFileForm.registerAttachFileButton = function (selector, params, dzOptions) {
    if ($uploadFileForm._attachFileButton[selector]) {
      return true;
    }
    $uploadFileForm._attachFileButton[selector] = {
      selector: selector,
      params: params,
      dzOptions: dzOptions
    };

    $(document).on('click', selector, function (e) {
      e.preventDefault();
      $uploadFileForm._setParams(this, params, dzOptions);
      $uploadFileAttachButton.trigger('click');
    });
  };

// 各ドロップエリアの設定パラメータをセットし直す
// ドロップエリアが切り替わる度に呼び出される
  $uploadFileForm._setParams = function (target, params, dzOptions) {
    var formID = (typeof params.formID == 'function') ? params.formID.call(target) : params.formID;
    var previewContainerID = (typeof params.previewContainerID == 'function') ? params.previewContainerID.call(target) : params.previewContainerID;
    $uploadFileForm._params.formID = formID;
    $uploadFileForm._params.previewContainerID = previewContainerID;

    // Dropzone 設定を上書き
    // （Dropzone インスタンスは常に１つ）
    Dropzone.instances[0].options = $.extend({}, $uploadFileForm._dzDefaultOptions, dzOptions || {});
    // acceptedFiles の設定は上書きされないので手動で設定
    Dropzone.instances[0].hiddenFileInput.setAttribute("accept", Dropzone.instances[0].options.acceptedFiles);
    // maxFiles が 1 の場合、もしくは
    // params.disableMultiple が true の場合 multiple 属性を外す（iphone で「画像選択」と「写真を撮る」を選択出来るようにする）
    if (Dropzone.instances[0].options.maxFiles == 1 || params.disableMultiple) {
      Dropzone.instances[0].hiddenFileInput.removeAttribute("multiple");
    } else {
      Dropzone.instances[0].hiddenFileInput.setAttribute("multiple", "multiple");
    }
    // フォームごとに仮アップロード中のファイルリストを持つ
    if (!$uploadFileForm._files[formID]) {
      $uploadFileForm._files[formID] = [];
    }
    Dropzone.instances[0].files = $uploadFileForm._files[formID];


    // コールバック関数登録
    var empty = function () {
    };
    $uploadFileForm._callbacks[previewContainerID] = {
      beforeAddedFile: params.beforeAddedFile ? params.beforeAddedFile : empty,
      beforeAccept: params.beforeAccept ? params.beforeAccept : empty,
      afterAccept: params.afterAccept ? params.afterAccept : empty,
      afterRemoveFile: params.afterRemoveFile ? params.afterRemoveFile : empty,
      afterSuccess: params.afterSuccess ? params.afterSuccess : empty,
      beforeSending: params.beforeSending ? params.beforeSending : empty,
      afterQueueComplete: params.afterQueueComplete ? params.afterQueueComplete : empty
    };
  };

// アップロードフォーム内の子要素の dragenter/dragleave イベントのチェック用
  var uploadFileFormContentEnter = false;
  $('.upload-file-form-content').on('dragenter', function (e) {
    uploadFileFormContentEnter = true;
  });

// ドロップエリアから外れた時
  $uploadFileForm.on('dragleave', function (e) {
    if ($(e.target).hasClass('upload-file-form-content')) {
      uploadFileFormContentEnter = false;
      return;
    }
    if (uploadFileFormContentEnter) {
      return;
    }

    $(this).hide();
  });

// ファイルが１つでもアップロード中であれば true
  $uploadFileForm._sending = false;

// ファイルアップロード中に submit ボタン押された時の イベントハンドラ
  $uploadFileForm._forbitSubmit = function (e) {
    alert(cake.message.validate.dropzone_uploading_not_end);
    e.stopPropagation();
    e.preventDefault();
    return false;
  };

//////////////////////////////////////////////////
// ドロップエリアとファイル添付ボタンの登録
//////////////////////////////////////////////////

///////////////////////////////
// 投稿フォーム
///////////////////////////////
  var postParams = {
    formID: 'PostDisplayForm',
    previewContainerID: 'PostUploadFilePreview',
    beforeSending: function () {
      if ($uploadFileForm._sending) {
        return;
      }
      $uploadFileForm._sending = true;
      // ファイルの送信中はsubmitできないようにする(クリックはできるがsubmit処理は走らない)
      $('#PostSubmit').on('click', $uploadFileForm._forbitSubmit);
    },
    afterQueueComplete: function () {
      $uploadFileForm._sending = false;

      // フォームをsubmit可能にする
      $('#PostSubmit').off('click', $uploadFileForm._forbitSubmit);

      // 投稿文が入力されていれば submit ボタンを有効化、空であれば無効化
      if ($('#CommonPostBody').val().length == 0) {
        $('#PostSubmit').attr('disabled', 'disabled');
      }
      else {
        $('#PostSubmit').removeAttr('disabled');
      }
    }
  };
  $uploadFileForm.registerDragDropArea('#PostForm', postParams);
  $uploadFileForm.registerAttachFileButton('#PostUploadFileButton', postParams);

///////////////////////////////
// メッセンジャーフォーム
///////////////////////////////
  var messageParams = {
    formID: 'messageDropArea',
    previewContainerID: 'messageUploadFilePreviewArea',
    beforeSending: function (file) {
      if ($uploadFileForm._sending) {
        return;
      }
      $uploadFileForm._sending = true;
      // ファイルの送信中はsubmitできないようにする(クリックはできるがsubmit処理は走らない)
      $('#MessageSubmit').on('click', $uploadFileForm._forbitSubmit);
    },
    afterQueueComplete: function (file) {
      $uploadFileForm._sending = false;
      // フォームをsubmit可能にする
      $('#MessageSubmit').off('click', $uploadFileForm._forbitSubmit);
    },
    afterSuccess: function (file) {
      $("#message_submit_button").click(function () {
        if (typeof Dropzone.instances[0] !== "" && Dropzone.instances[0].files.length > 0) {
          // ajax で submit するので、アップロード完了後に Dropzone のファイルリストを空にする
          // （参照先の配列を空にするため空配列の代入はしない）
          Dropzone.instances[0].files.length = 0;
        }
      });
    }
  };
  var messageDzOptions = {
    maxFiles: 10
  };
  $uploadFileForm.registerDragDropArea('#messageDropArea', messageParams, messageDzOptions);
  $uploadFileForm.registerAttachFileButton('#messageUploadFileButton', messageParams, messageDzOptions);

///////////////////////////////
// アクションメイン画像（最初の画像選択時)
///////////////////////////////
  var actionImageParams = {
    formID: 'CommonActionDisplayForm',
    previewContainerID: 'ActionUploadFilePhotoPreview',
    beforeSending: function (file) {
      if ($uploadFileForm._sending) {
        return;
      }
      $uploadFileForm._sending = true;
      // ファイルの送信中はsubmitできないようにする(クリックはできるがsubmit処理は走らない)
      $('#CommonActionSubmit').on('click', $uploadFileForm._forbitSubmit);
    },
    beforeAccept: function (file) {
      var $oldPreview = $('#' + $uploadFileForm._params.previewContainerID).find('.dz-preview:visible');

      // 画像を２枚同時に選択（ドラッグ）された時の対応
      if ($oldPreview.size()) {
        // Dropzone の管理ファイルから外す
        var old_file = Dropzone.instances[0].files.splice(0, 1)[0];

        // プレビューエリアを非表示にする
        $oldPreview.hide();

        // フォームの hidden を削除
        $('#' + old_file.file_id).remove();

        // サーバ上から削除
        $removeFileForm.find('input[name="data[AttachedFile][file_id]"]').val(sanitize(old_file.file_id));
        $.ajax({
          url: cake.url.remove_file,
          type: 'POST',
          dataType: 'json',
          processData: false,
          data: $removeFileForm.serialize()
        })
          .done(function (res) {
            // pass
          })
          .fail(function (res) {
            // pass
          });
      }
    },
    afterAccept: function (file) {
      var $button = $('.post-action-image-add-button');
      if ($button.size()) {
        evTargetShowThisDelete.call($button.get(0));
      }
      $('#GoalSelectOnActionForm').trigger('change');
      $(file.previewTemplate).show();
    },
    afterQueueComplete: function (file) {
      $uploadFileForm._sending = false;
      // フォームをsubmit可能にする
      $('#CommonActionSubmit').off('click', $uploadFileForm._forbitSubmit);
    },
  };
  var actionImageDzOptions = {
    acceptedFiles: "image/*",
    maxFiles: 1,
    previewTemplate: previewTemplateActionImage
  };
  $uploadFileForm.registerDragDropArea('#ActionImageAddButton', actionImageParams, actionImageDzOptions);
  $uploadFileForm.registerAttachFileButton('#ActionImageAddButton', actionImageParams, actionImageDzOptions);

///////////////////////////////
// アクションメイン画像（入れ替え時）
///////////////////////////////
  var actionImage2Params = {
    formID: 'CommonActionDisplayForm',
    previewContainerID: 'ActionUploadFilePhotoPreview',
    disableMultiple: true,
    beforeSending: function (file) {
      if ($uploadFileForm._sending) {
        return;
      }
      $uploadFileForm._sending = true;
      // ファイルの送信中はsubmitできないようにする(クリックはできるがsubmit処理は走らない)
      $('#CommonActionSubmit').on('click', $uploadFileForm._forbitSubmit);
    },
    beforeAccept: function (file) {
      var $oldPreview = $('#' + $uploadFileForm._params.previewContainerID).find('.dz-preview:visible');

      // Dropzone の管理ファイルから外す
      var old_file = Dropzone.instances[0].files.splice(0, 1)[0];

      // プレビューエリアを非表示にする
      $oldPreview.hide();

      // 既にDBに保存済のデータの場合（アクション編集時）
      if (old_file.saved_file) {
        // フォームの hidden を削除
        $('#AttachedFile_' + old_file.file_id).remove();

        // 削除済ファイルの hidden を追加
        var $form = $('#' + $uploadFileForm._params.formID);
        $form.append($('<input type=hidden name=data[deleted_file_id][]>').val(sanitize(old_file.file_id)));
      }
      // 新しくアップロードするファイルの場合
      else {
        // フォームの hidden を削除
        $('#' + old_file.file_id).remove();

        // サーバ上から削除
        $removeFileForm.find('input[name="data[AttachedFile][file_id]"]').val(sanitize(old_file.file_id));
        $.ajax({
          url: cake.url.remove_file,
          type: 'POST',
          dataType: 'json',
          processData: false,
          data: $removeFileForm.serialize()
        })
          .done(function (res) {
            // pass
          })
          .fail(function (res) {
            // pass
          });
      }
    },
    afterAccept: function (file) {
      $(file.previewTemplate).show();
    },
    afterSuccess: function (file) {
      // メイン画像の hidden を先頭に持ってくる
      // DB内の index 番号を 0 にするため
      var $form = $('#' + $uploadFileForm._params.formID);
      var file_id = $(file.previewTemplate).data('file_id');
      var $firstHidden = $form.find('input[name="data[file_id][]"]:first');
      if ($firstHidden.val() != file_id) {
        $('#' + file_id).insertBefore($firstHidden);
      }
    },
    afterQueueComplete: function (file) {
      $uploadFileForm._sending = false;
      // フォームをsubmit可能にする
      $('#CommonActionSubmit').off('click', $uploadFileForm._forbitSubmit);
    },
  };
  var actionImage2DzOptions = {
    acceptedFiles: "image/*",
    previewTemplate: previewTemplateActionImage
  };
  $uploadFileForm.registerDragDropArea('.action-photo-preview', actionImage2Params, actionImage2DzOptions);
  $uploadFileForm.registerAttachFileButton('.action-photo-preview', actionImage2Params, actionImage2DzOptions);

///////////////////////////////
// アクション添付ファイル
///////////////////////////////
  var actionParams = {
    formID: 'CommonActionDisplayForm',
    previewContainerID: 'ActionUploadFilePreview',
    afterAccept: actionImageParams.afterAccept,
    beforeSending: function () {
      if ($uploadFileForm._sending) {
        return;
      }
      $uploadFileForm._sending = true;
      // ファイルの送信中はsubmitできないようにする(クリックはできるがsubmit処理は走らない)
      $('#CommonActionShare').on('click', $uploadFileForm._forbitSubmit);
    },
    afterQueueComplete: function () {
      $uploadFileForm._sending = false;
      // フォームをsubmit可能にする
      $('#CommonActionShare').off('click', $uploadFileForm._forbitSubmit);
      $('#CommonActionShare').removeAttr('disabled')
    }
  };
  $uploadFileForm.registerDragDropArea('#ActionUploadFileDropArea', actionParams);
  $uploadFileForm.registerAttachFileButton('#ActionFileAttachButton', actionParams);

//////////////////////////////////////////////////
// Dropzone 有効化
//////////////////////////////////////////////////
  $uploadFileForm.dropzone();
  if (typeof Dropzone.instances[0] !== "undefined") {
    $uploadFileForm._dzDefaultOptions = $.extend({}, Dropzone.instances[0].options);
  }
  $(document).data('uploadFileForm', $uploadFileForm);

//////////////////////////////////////////////////
// 投稿、アクション の編集時の処理
//////////////////////////////////////////////////

// DB に保存済の添付ファイルデータを Dropzone に手動で登録する
  var dropzonePrepareEdit = function (setting) {
    var $input = $(this);

    var file = {};
    file.saved_file = true;
    file.name = $input.attr('data-name');
    file.size = $input.attr('data-size');

    file.upload = {
      progress: 100,
      total: file.size,
      bytesSent: file.size
    };
    file.status = Dropzone.SUCCESS;

    $uploadFileForm._setParams(setting.selector, setting.params, setting.dzOptions);
    Dropzone.instances[0].files.push(file);
    Dropzone.instances[0].options.addedfile.call(Dropzone.instances[0], file);
    file.previewElement.classList.remove("dz-file-preview");
    file.previewElement.querySelector('.progress').style.visibility = 'hidden';

    switch ($input.attr('data-ext').toLowerCase()) {
      case 'jpg':
      case 'jpeg':
      case 'gif':
      case 'png':
        var thumb = file.previewElement.querySelector("[data-dz-thumbnail]");
        if (thumb) {
          thumb.alt = file.name;
          thumb.src = $input.attr('data-url');
          thumb.classList.remove('none');
        }
        var icon = file.previewElement.querySelector("i[class*=file-other-icon]");
        if (icon) {
          icon.classList.add('none');
        }
        break;

      default:
        break;
    }
    file.file_id = $input.attr('value');
    $(file.previewElement).data('file_id', file.file_id).show();
  };

// registerDragDropArea() か registerAttachFileButton() で登録されたフォームをチェックし、
// <input type=hidden name=data[file_id][]> が存在すれば、Dropzone に初期データとして登録する
  var settings = {};
  var i, setting;
  for (i in $uploadFileForm._dragDropArea) {
    if (!$uploadFileForm._dragDropArea.hasOwnProperty(i)) {
      continue;
    }
    setting = $uploadFileForm._dragDropArea[i];
    settings[setting.params.previewContainerID] = setting;
  }
  for (i in $uploadFileForm._attachFileButton) {
    if (!$uploadFileForm._attachFileButton.hasOwnProperty(i)) {
      continue;
    }
    setting = $uploadFileForm._attachFileButton[i];
    settings[setting.params.previewContainerID] = setting;
  }
  for (i in settings) {
    if (!settings.hasOwnProperty(i)) {
      continue;
    }
    var $hiddens = $('#' + settings[i].params.formID).find('input[type=hidden][name="data[file_id][]"]');
    if (!$hiddens.size()) {
      continue;
    }

    var previewContainerID = settings[i].params.previewContainerID;
    // アクションのメイン画像の場合
    // hidden の最初の１件のみ処理
    if (previewContainerID == 'ActionUploadFilePhotoPreview') {
      dropzonePrepareEdit.call($hiddens.eq(0).get(0), settings[i]);
    }
    // アクションの添付ファイルの場合
    // hidden の最初の１件以外を処理
    else if (previewContainerID == 'ActionUploadFilePreview') {
      $hiddens.not(':first').each(function () {
        dropzonePrepareEdit.call(this, settings[i]);
      });
    }
    else {
      $hiddens.each(function () {
        dropzonePrepareEdit.call(this, settings[i]);
      });
    }
  }
});
