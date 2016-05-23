import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class CircleCreate extends React.Component {
  constructor(props) {
    super(props);
    bindSelect2Members($('#modal_add_circle'))
    this.state = {
      selected_privacy_value: 1
    }
  }
  componentDidMount() {
    // Set data attributes
    ReactDOM.findDOMNode(this.refs.members).setAttribute("data-url", "/users/ajax_select2_get_users?with_group=1")

  }
  onSubmitCircle(event) {
    event.preventDefault()
    this.props.onSubmitCircle(this.getInputDomData())
  }
  getInputDomData() {
    return {
      circle_name: ReactDOM.findDOMNode(this.refs.circle_name).value.trim(),
      public_flg: this.state.selected_privacy_value,
      circle_description: ReactDOM.findDOMNode(this.refs.circle_description).value.trim(),
      members: ReactDOM.findDOMNode(this.refs.members).value,
      circle_image: ReactDOM.findDOMNode(this.refs.circle_image).files[0]
    }
  }
  render() {
    return (
      <div id="modal_add_circle">
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Join a circle")}
        </div>
        <form onSubmit={e => {this.onSubmitCircle(e)}}
              className="form-horizontal setup-circle-create-form"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              ref="circle_form">
          <div className="panel-body">
            <span className="help-block">{__("Circle name")}</span>
            <input ref="circle_name" className="form-control addteam_input-design"
                   onChange={() => {this.props.toggleButtonClickable(this.getInputDomData()) }} />
          </div>
          <div className="panel-body">
            <span className="help-block">{__("Members")}</span>
            <div className="ddd">
                <input type="hidden" name="members" ref="members" className="ajax_add_select2_members select2-offscreen" id="CircleMembers" />
                <span className="help-block font_11px">{__("Administrators")}</span>
            </div>
          </div>
          <div className="panel-body setup-circle-public-group">
              <input type="radio" ref="public" name="public_flg" id="CirclePublicFlg1" value="1" defaultChecked="checked" onChange={() => {this.state = {selected_privacy_value: 1}}} /> {__("Public")}
              <span className="help-block font_11px">
                {__("Anyone can see the circle, its members and their posts.")}</span>
              <input type="radio" ref="privacy" name="public_flg" id="CirclePublicFlg0" value="0" onChange={() => {this.state = {selected_privacy_value: 0}}} /> {__("Privacy")}
              <span className="help-block font_11px">
                {__("Only members can find the circle and see posts.")}
              </span>
          </div>
          <div className="panel-body">
            <span className="help-block">{__("Circle Description")}</span>
            <input ref="circle_description" className="form-control addteam_input-design" onChange={() => {this.props.toggleButtonClickable(this.getInputDomData()) }} />
          </div>
          <div className="panel-body">
            <span className="help-block">{__("Circle Image")}</span>
            <div className="form-inline">
              <div className="fileinput_small fileinput-new" data-provides="fileinput">
                <div className="fileinput-preview thumbnail nailthumb-container photo-design form-group setup-guide-file-preview" data-trigger="fileinput">
                  <i className="fa fa-plus photo-plus-large"></i>
                </div>
                <div className="form-group">
                  <div className="btn btn-default btn-file ml_16px setup-guide-file-select-btn">
                    <span className="fileinput-new">{__("Select an image")}</span>
                    <span className="fileinput-exists">{__("Reselect an image")}</span>
                    <input type="file" name="circle_image" ref="circle_image" className="form-control addteam_input-design" id="GoalPhoto" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div>
            <Link className="btn btn-secondary setup-back-btn"
                  to={this.props.circle.circles.length ? "/setup/circle/select" : "/setup/circle/image"}>
              {__('Back')}
            </Link>
            <input type="submit" className="btn btn-primary setup-next-btn pull-right" defaultValue={__("Create")} disabled={!Boolean(this.props.circle.can_click_submit_button)} />
          </div>
        </form>
      </div>
    )
  }
}

CircleCreate.propTypes = {
  onSubmitCircle: PropTypes.func
}
