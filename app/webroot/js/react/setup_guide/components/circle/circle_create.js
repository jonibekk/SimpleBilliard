import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class CircleCreate extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> Join a circle
        </div>
        <div className="modal-header">
            <button type="button" className="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                <span className="close-icon">&times;</span></button>
            <h4 className="modal-title">Create a circle</h4>
        </div>
        <form>
          <div className="modal-body modal-circle-body">
            <input ref="name" label="Circle name" />
              <div className="form-group">
                  <label className="ccc control-label modal-label">Members</label>
                  <div className="ddd">
                      <span className="help-block font_11px">Administrators</span>
                  </div>
              </div>
              <div className="font_brownRed font_11px">
                  {() => {"You can't change this setting lator"}}
              </div>
              <div className="form-group">
                <label className="eee control-label modal-label">プライバシー</label>
                <label className="">
                  <input type="radio" name="data[Circle][public_flg]" id="CirclePublicFlg1" value="1" checked="checked" /> 公開
                  <span className="help-block font_11px">
                    サークル名と参加メンバー、投稿がチーム内に公開されます。チームメンバーは誰でも自由に参加できます。</span>
                </label>
                <label className="">
                  <input type="radio" name="data[Circle][public_flg]" id="CirclePublicFlg0" value="0" /> 秘密
                  <span className="help-block font_11px">
                    サークル名と参加メンバー、投稿はこのサークルの参加メンバーだけに表示されます。サークル管理者だけがメンバーを追加できます。
                    </span>
                </label>
              </div>
              <div className="form-group">
                  <label className="f control-label modal-label">Circle Image</label>
                  <div className="ggg">
                      <div className="fileinput_small fileinput-new" data-provides="fileinput">
                          <div className="fileinput-preview thumbnail nailthumb-container photo-design"
                               data-trigger="fileinput">
                              <i className="fa fa-plus photo-plus-large"></i>
                          </div>
                          <span className="btn btn-default btn-file">
                              <span className="fileinput-new">
                                  Select an image
                              </span>
                              <span className="fileinput-exists">Reselect an image</span>
                             <input type="file" name="photo" className="form-control addteam_input-design" id="GoalPhoto" />
                          </span>
                          <span className="help-block font_11px inline-block">Smaller than 10MB</span>
                      </div>
                  </div>
              </div>
          </div>
          <div className="panel-body">
            <Link to="/setup/circle/select" className="btn btn-secondary setup-back-btn">Back</Link>
            <input type="submit" className="btn btn-primary" defaultValue="Create a goal" />
          </div>
        </form>
      </div>
    )
  }
}
