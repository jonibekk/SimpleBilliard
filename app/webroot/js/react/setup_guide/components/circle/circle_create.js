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
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Join a circle")}
        </div>
        <div className="modal-header">
            <h4 className="modal-title">{__("Create a circle")}</h4>
        </div>
        <form>
          <div className="modal-body modal-circle-body">
            <input ref="name" label="Circle name" />
              <div className="form-group">
                  <label className="ccc control-label modal-label">{__("Members")}</label>
                  <div className="ddd">
                      <span className="help-block font_11px">Administrators</span>
                  </div>
              </div>
              <div className="font_brownRed font_11px">
                  {__("You can't change this setting lator")}
              </div>
              <div className="form-group">
                <label className="">
                  <input type="radio" name="data[Circle][public_flg]" id="CirclePublicFlg1" value="1" checked="checked" /> {__("Public")}
                  <span className="help-block font_11px">
                    {__("Anyone can see the circle, its members and their posts.")}</span>
                </label>
                <label className="">
                  <input type="radio" name="data[Circle][public_flg]" id="CirclePublicFlg0" value="0" /> {__("Privacy")}
                  <span className="help-block font_11px">
                    {__("Only members can find the circle and see posts.")}
                    </span>
                </label>
              </div>
              <div className="form-group">
                  <label className="f control-label modal-label">{__("Circle Image")}</label>
                  <div className="ggg">
                      <div className="fileinput_small fileinput-new" data-provides="fileinput">
                          <div className="fileinput-preview thumbnail nailthumb-container photo-design"
                               data-trigger="fileinput">
                              <i className="fa fa-plus photo-plus-large"></i>
                          </div>
                          <span className="btn btn-default btn-file">
                              <span className="fileinput-new">
                                  {__("Select an image")}
                              </span>
                              <span className="fileinput-exists">Reselect an image</span>
                             <input type="file" name="photo" className="form-control addteam_input-design" id="GoalPhoto" />
                          </span>
                          <span className="help-block font_11px inline-block">{__("Smaller than 10MB")}</span>
                      </div>
                  </div>
              </div>
          </div>
          <div className="panel-body">
            <Link to="/setup/circle/select" className="btn btn-secondary setup-back-btn">{__("Back")}</Link>
            <input type="submit" className="btn btn-primary" defaultValue="Create a goal" />
          </div>
        </form>
      </div>
    )
  }
}
