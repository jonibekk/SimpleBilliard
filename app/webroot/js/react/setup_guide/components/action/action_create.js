import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class ActionCreate extends React.Component {
  constructor(props) {
    super(props)
  }
  componentWillMount() {
    if(Object.keys(this.props.action.selected_action_goal).length === 0) {
      browserHistory.push('/setup/action/goal_select')
    }
    let pusher = new Pusher(cake.pusher.key)
    pusher.connection.bind('connected', function () {
        cake.pusher.socket_id = pusher.connection.socket_id
    })
  }
  componentDidMount() {
    // Set data attributes
    ReactDOM.findDOMNode(this.refs.ActionImageAddButton).setAttribute("target-id", "CommonActionSubmit,WrapActionFormName,WrapCommonActionGoal,CommonActionFooter,CommonActionFormShowOptionLink,ActionUploadFileDropArea")
    ReactDOM.findDOMNode(this.refs.ActionImageAddButton).setAttribute("delete-method", "hide")

    // Dropzoneファイルアップロード上限数のリセット
    if (typeof Dropzone.instances[0] !== undefined && Dropzone.instances[0].files.length > 0) {
        Dropzone.instances[0].files.length = 0;
    }
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Do an action")}
        </div>
        <div className="panel panel-default global-form setup-post-form" id="GlobalForms">
          <div className="post-panel-heading ptb_7px plr_11px">
            <ul className="feed-switch clearfix plr_0px" id="CommonFormTabs">
              <li className="switch-action">
                <Link to="#"
                   className="switch-action-anchor click-target-focus">
                   <i className="fa fa-check-circle"></i>{__("Action")}
                </Link>
                <span className="switch-arrow"></span>
              </li>
            </ul>
          </div>
          <div className="tab-content">
            <div id="ActionForm">
              <form id="CommonActionDisplayForm" encType="multipart/form-data" method="post"
                    acceptCharset="utf-8" type="file" className="form-feed-notify"
                    onSubmit={(e) => this.props.onSubmitAction(e, this.refs, cake.pusher.socket_id, this.props.action.selected_action_goal.id)}
                    action=""
                    >
                <div className="post-panel-body plr_11px ptb_7px">
                  <a href="#"
                     id="ActionImageAddButton"
                     className="post-action-image-add-button"
                     ref="ActionImageAddButton">
                    <span className="action-image-add-button-text">
                      <i className="fa fa-image action-image-add-button-icon"></i>
                      <span>{__('Upload an image as your action')}</span>
                    </span>
                  </a>
                </div>
                <div id="ActionUploadFilePhotoPreview" className="pull-left action-upload-main-image-preview"></div>
                <div id="WrapActionFormName" className="panel-body action-form-panel-body none pull-left action-input-name">
                  <textarea name="body" ref="body" className="form-control change-warning" placeholder={__("Write an action...")} onChange={() => {this.props.onChangeTextField(this.refs)}}></textarea>
                </div>
                <div id="ActionUploadFileDropArea" className="action-upload-file-drop-area">
                  <div id="ActionUploadFilePreview" className="action-upload-file-preview">
                  </div>
                  <div className="panel-body action-form-panel-body form-group none" id="WrapCommonActionGoal">
                    <div className="input-group feed-action-goal-select-wrap">
                      <span className="input-group-addon" id=""><i className="fa fa-flag"></i></span>
                      <select className="form-control change-next-select-with-value">
                        <option>{this.props.action.selected_action_goal.name}</option>
                      </select>
                    </div>
                  </div>
                  <div className="post-panel-footer none" id="CommonActionFooter">
                    <div className="font_12px" id="CommonActionFormFooter">
                      <a href="#" className="link-red" id="ActionFileAttachButton">
                        <button type="button" className="btn pull-left photo-up-btn"><i
                                className="fa fa-paperclip post-camera-icon"></i>
                        </button>
                      </a>
                      <div className="row form-horizontal form-group post-share-range" id="CommonActionShare">
                        <input type="submit" className="btn btn-primary pull-right post-submit-button" id="CommonActionSubmit" disabled={!Boolean(this.props.action.can_click_submit_button)} />
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div>
          <Link className="btn btn-secondary setup-back-btn-full"
                to={this.props.action.goals.length ? "/setup/action/goal_select" : "/setup/action/image"}>
            {__('Back')}
          </Link>
        </div>
      </div>
    )
  }
}
