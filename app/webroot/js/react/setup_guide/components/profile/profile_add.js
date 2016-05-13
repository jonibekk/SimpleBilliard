import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class ProfileAdd extends React.Component {
  constructor(props) {
    super(props);
  }
  componentWillMount() {
    this.props.fetchDefaultProfile()
  }
  componentDidMount() {
    this.props.toggleButtonClickable(this.getInputDomData())
  }
  getInputDomData() {
    return {
      comment: ReactDOM.findDOMNode(this.refs.comment).value.trim()
    }
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Input your profile")}
        </div>
        <form onSubmit={e => {this.props.onSubmitProfile(e, this.refs)}}
              className="form-horizontal setup-circle-create-form"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8">
          <div className="panel-body">
            <span className="help-block">{__("Your profile picture")}</span>
            <div className="form-inline">
              <div className="fileinput_small fileinput-new" data-provides="fileinput">
                <div className="fileinput-preview thumbnail nailthumb-container photo-design form-group" data-trigger="fileinput">
                  <img src={this.props.profile.default_profile.photo_file_path} className="lazy" alt='' />
                </div>
                <div className="form-group">
                  <span className="btn btn-default btn-file ml_16px">
                  <span className="fileinput-new">{__("Select an image")}</span>
                  <span className="fileinput-exists">{__("Reselect an image")}</span>
                    <input type="file" name="profile_image" ref="profile_image" className="form-control addteam_input-design" onChange={() => {this.props.toggleButtonClickable(this.getInputDomData())}} />
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div className="panel-body">
            <span className="help-block">{__("Your self-info")}</span>
            <textarea ref="comment" className="form-control addteam_input-design" rows="6"
                      onChange={() => {this.props.toggleButtonClickable(this.getInputDomData())}} defaultValue={this.props.profile.default_profile.comment} />
          </div>
          <div>
            <Link to="/setup/profile/image" className="btn btn-secondary setup-back-btn">{__('Back')}</Link>
            <input type="submit" className="btn btn-primary setup-next-btn pull-right" defaultValue={__("Submit")}
            disabled={!Boolean(this.props.profile.can_click_submit_button)} />
          </div>
        </form>
      </div>
    )
  }
}

ProfileAdd.propTypes = {
  onSubmitProfile: PropTypes.func,
  toggleButtonClickable: PropTypes.func,
  can_click_submit_button: PropTypes.bool
}
