<div id="setup-guide-app">
  <div class="setup-container col col-sm-8 col-sm-offset-2 panel">
    <div class="setup-inner col col-xxs-10 col-xxs-offset-1 pb_8px pt_20px font_verydark">
      <!-- Setup guide header -->
      <div class="setup-pankuzu font_18px">
        <?= __("Set up Goalous") ?> < <?= __("Create a goal") ?>
      </div>
      <form class="form-horizontal bv-form" novalidate="novalidate" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <div class="panel-body add-team-panel-body">
          <span class="help-block"><?= __("Goal Name") ?></span>
          <textarea name="data[Goal][name]" class="form-control addteam_input-design" placeholder="詳しく書く。" data-bv-notempty-message="入力必須項目です。" required="required" rows="1" data-bv-stringlength="true" data-bv-stringlength-max="200" data-bv-stringlength-message="最大文字数(200)を超えています。" cols="30" id="GoalName" data-bv-field="data[Goal][name]" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 38px;"></textarea>
        </div>
        <div class="panel-body add-team-panel-body">
          <div class="form-inline">
            <div class="form-group">
              <span class="help-block"><?= __("Unit") ?></span>
              <select name="data[Goal][value_unit]" class="change-select-target-hidden form-control addteam_input-design" target-id="KeyResult0ValueInputWrap" required="required" hidden-option-value="2" id="GoalValueUnit" data-bv-field="data[Goal][value_unit]">
                <option value="0">%</option>
                <option value="3">¥</option>
                <option value="4">$</option>
                <option value="1">その他の単位</option>
                <option value="2">なし</option>
              </select>
            </div>
            <div class="form-group">
              <span class="help-block"><?= __("Initial point") ?></span>
              <input name="data[Goal][start_value]" class="form-control addteam_input-design" step="0.1" required="required" data-bv-stringlength="true" data-bv-stringlength-max="15" data-bv-stringlength-message="最大文字数(15)を超えています。" data-bv-notempty-message="入力必須項目です。" data-bv-numeric-message="数字を入力してください。" type="number" value="0" id="GoalStartValue" data-bv-field="data[Goal][start_value]">
            </div>
            <div class="form-group setup-form-arrow">
              <i class="fa fa-arrow-right font_18px"></i>
            </div>
            <div class="form-group">
              <span class="help-block"><?= __("Achieve point") ?></span>
              <input name="data[Goal][target_value]" class="form-control addteam_input-design" step="0.1" required="required" data-bv-stringlength="true" data-bv-stringlength-max="15" data-bv-stringlength-message="最大文字数(15)を超えています。" data-bv-notempty-message="入力必須項目です。" data-bv-numeric-message="数字を入力してください。" type="number" value="100" id="GoalTargetValue" data-bv-field="data[Goal][target_value]">
            </div>
          </div>
        </div>
        <div class="panel-body add-team-panel-body">
          <span class="help-block"><?= __("Due Date") ?></span>
          <div class="input-group date goal-set-date">
              <input name="data[Goal][end_date]" class="form-control" value="2016/09/30" default="2016/09/30" required="required" data-bv-notempty-message="入力必須項目です。" data-bv-stringlength="true" data-bv-stringlength-max="10" data-bv-stringlength-message="最大文字数(10)を超えています。" type="text" id="GoalEndDate" data-bv-field="data[Goal][end_date]">
              <span class="input-group-addon"><i class="fa fa-th"></i></span>
          </div>
        </div>
        <div class="panel-body add-team-panel-body">
          <span class="help-block"><?= __("Goal Image") ?></span>
          <div class="form-inline">
            <div class="fileinput_small fileinput-new" data-provides="fileinput">
              <div class="fileinput-preview thumbnail nailthumb-container photo-design form-group" data-trigger="fileinput" style="width: 96px; height: 96px; line-height: 96px;">
                <i class="fa fa-plus photo-plus-large"></i>
              </div>
              <div class="form-group">
                <span class="btn btn-default btn-file ml_16px">
                  <span class="fileinput-new">画像を選択</span>
                  <span class="fileinput-exists">画像を再選択</span>
                  <input type="hidden"><input type="file" name="data[Goal][photo]" class="form-control addteam_input-design" id="GoalPhoto" data-bv-field="data[Goal][photo]">                           </span>
                <span class="help-block inline-block font_11px">10MB以下</span>
              </div>
            </div>
          </div>
        </div>
      </form>
      <a href="/setup/" class="btn btn-primary"><?= __("Create a goal") ?></a>
  </div>
</div>
