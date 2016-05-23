import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class ProfileAdd extends React.Component {
  constructor(props) {
    super(props)
  }
  componentWillMount() {
    this.props.fetchDefaultProfile()
  }
  componentDidMount() {
    if(this.props.profile.default_profile.comment) {
      this.props.enableSubmitButton()
    }
  }
  getInputDomData() {
    return {
      comment: ReactDOM.findDOMNode(this.refs.comment).value.trim(),
      profile_image: ReactDOM.findDOMNode(this.refs.profile_image).files[0]
    }
  }
  render() {
    // FIXME: コンポーネントマウント時のデフォルト画像表示をもっとスマートに書く
    let not_exist_image_fa_style = {
      display: this.props.profile.default_profile.photo_file_name ? 'none' : 'inline-block'
    }
    let default_image_style = {
      display: this.props.profile.default_profile.photo_file_name ? 'inline-block' : 'none'
    }

    // FIXME: コンポーネントマウント時のプロフィールコメントの表示のもっとスマートに書く(できればjQuery使わない方法で)
    if(!$('.setup-guide-add-profile-textarea').val() && !this.props.profile.textarea_changed) {
      $('.setup-guide-add-profile-textarea').val(this.props.profile.default_profile.comment)
    }
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Input your profile")}
        </div>
        <form className="form-horizontal setup-circle-create-form"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              onSubmit={e => {
                e.preventDefault()
                this.props.onSubmitProfile(this.getInputDomData())
              }}>
          <div className="panel-body">
            <span className="help-block">{__("Your profile picture")}</span>
            <div className="form-inline">
              <div className="fileinput_small fileinput-new" data-provides="fileinput">
                <div className="fileinput-preview thumbnail nailthumb-container photo-design setup-guide-file-preview" data-trigger="fileinput">
                  <i className="fa fa-plus photo-plus-large" style={not_exist_image_fa_style}></i>
                  <img src={this.props.profile.default_profile.photo_file_path} style={default_image_style} />
                </div>
                <div className="form-group">
                  <div className="btn btn-default btn-file ml_16px setup-guide-file-select-btn">
                    <span className="fileinput-new">{__("Select an image")}</span>
                    <span className="fileinput-exists">{__("Reselect an image")}</span>
                    <input type="file" name="profile_image" ref="profile_image" className="form-control addteam_input-design" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="panel-body setup-profile-form">
            <span className="help-block">{__("Your self-info")}</span>
            <textarea ref="comment" className="form-control addteam_input-design setup-guide-add-profile-textarea"
                      rows="6" name="comment" cols="5" maxLength="10000"
                      onChange={() => {this.props.toggleButtonClickable(this.getInputDomData())}} />
          </div>
          <div>
            <Link to="/setup/profile/image" className="btn btn-secondary setup-back-btn">{__('Back')}</Link>
            <input type="submit" className="btn btn-primary setup-next-btn pull-right"
                   defaultValue={__("Submit")}
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
