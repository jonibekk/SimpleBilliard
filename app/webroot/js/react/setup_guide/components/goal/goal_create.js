import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class GoalCreate extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          :Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> :Create a goal
        </div>
        <form className="form-horizontal bv-form" novalidate="novalidate" enctype="multipart/form-data" method="post" accept-charset="utf-8">
          <div className="panel-body add-team-panel-body">
            <span className="help-block">Goal Name</span>
            <textarea name="data[Goal][name]" className="form-control addteam_input-design" placeholder="詳しく書く。" data-bv-notempty-message="入力必須項目です。" required="required" rows="1" data-bv-stringlength="true" data-bv-stringlength-max="200" data-bv-stringlength-message="最大文字数(200)を超えています。" cols="30" id="GoalName" data-bv-field="data[Goal][name]" ></textarea>
          </div>
          <div className="panel-body add-team-panel-body">
            <div className="form-inline">
              <div className="form-group">
                <span className="help-block">Unit</span>
                <select name="data[Goal][value_unit]" className="change-select-target-hidden form-control addteam_input-design" target-id="KeyResult0ValueInputWrap" required="required" hidden-option-value="2" id="GoalValueUnit" data-bv-field="data[Goal][value_unit]">
                  <option value="0">%</option>
                  <option value="3">¥</option>
                  <option value="4">$</option>
                  <option value="1">その他の単位</option>
                  <option value="2">なし</option>
                </select>
              </div>
              <div className="form-group">
                <span className="help-block">Initial point</span>
                <input name="data[Goal][start_value]" className="form-control addteam_input-design" step="0.1" required="required" data-bv-stringlength="true" data-bv-stringlength-max="15" data-bv-stringlength-message="最大文字数(15)を超えています。" data-bv-notempty-message="入力必須項目です。" data-bv-numeric-message="数字を入力してください。" type="number" value="0" id="GoalStartValue" data-bv-field="data[Goal][start_value]" />
              </div>
              <div className="form-group setup-form-arrow">
                <i className="fa fa-arrow-right font_18px"></i>
              </div>
              <div className="form-group">
                <span className="help-block">Achieve point</span>
                <input name="data[Goal][target_value]" className="form-control addteam_input-design" step="0.1" required="required" data-bv-stringlength="true" data-bv-stringlength-max="15" data-bv-stringlength-message="最大文字数(15)を超えています。" data-bv-notempty-message="入力必須項目です。" data-bv-numeric-message="数字を入力してください。" type="number" value="100" id="GoalTargetValue" data-bv-field="data[Goal][target_value]" />
              </div>
            </div>
          </div>
          <div className="panel-body add-team-panel-body">
            <span className="help-block">Due Date</span>
            <div className="input-group date goal-set-date">
                <input name="data[Goal][end_date]" className="form-control" value="2016/09/30" default="2016/09/30" required="required" data-bv-notempty-message="入力必須項目です。" data-bv-stringlength="true" data-bv-stringlength-max="10" data-bv-stringlength-message="最大文字数(10)を超えています。" type="text" id="GoalEndDate" data-bv-field="data[Goal][end_date]" />
                <span className="input-group-addon"><i className="fa fa-th"></i></span>
            </div>
          </div>
          <div className="panel-body add-team-panel-body">
            <span className="help-block">Goal Image</span>
            <div className="form-inline">
              <div className="fileinput_small fileinput-new" data-provides="fileinput">
                <div className="fileinput-preview thumbnail nailthumb-container photo-design form-group" data-trigger="fileinput">
                  <i className="fa fa-plus photo-plus-large"></i>
                </div>
                <div className="form-group">
                  <span className="btn btn-default btn-file ml_16px">
                    <span className="fileinput-new">画像を選択</span>
                    <span className="fileinput-exists">画像を再選択</span>
                    <input type="file" name="data[Goal][photo]" className="form-control addteam_input-design" id="GoalPhoto" data-bv-field="data[Goal][photo]" />
                  </span>
                  <span className="help-block inline-block font_11px">10MB以下</span>
                </div>
              </div>
            </div>
          </div>
        </form>
        <Link to="/setup" className="btn btn-primary">Create a goal</Link>
      </div>
    )
  }
}
