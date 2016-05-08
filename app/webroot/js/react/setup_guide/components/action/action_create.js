import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'

export default class ActionCreate extends React.Component {
  constructor(props) {
    super(props)
  }
  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.ActionImageAddButton).setAttribute("target-id", "CommonActionSubmit,WrapActionFormName,WrapCommonActionGoal,CommonActionFooter,CommonActionFormShowOptionLink,ActionUploadFileDropArea")
    ReactDOM.findDOMNode(this.refs.ActionImageAddButton).setAttribute("delete-method", "hide")
  }
  render() {
    const options = () => {
      return (
        <option value="8">Lunch with team members</option>
      )
    }
    const goal_select = () => {
      return (
        <select name="goal_id" className="form-control change-next-select-with-value" id="GoalSelectOnActionForm">
          <option value="">{__('Select a goal.')}</option>
          {options}
        </select>
      )
    }
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Do an action")}
        </div>
        <div className="panel panel-default global-form" id="GlobalForms">
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
              <form id="CommonActionDisplayForm" encType="multipart/form-data" method="post" acceptCharset="utf-8">
                <div className="post-panel-body plr_11px ptb_7px">
                  <Link to="#"
                     id="ActionImageAddButton"
                     className="post-action-image-add-button"
                     ref="ActionImageAddButton"
                     target-id="CommonActionSubmit,WrapActionFormName,WrapCommonActionGoal,CommonActionFooter,CommonActionFormShowOptionLink,ActionUploadFileDropArea"
                     delete-method="hide">
                    <span className="action-image-add-button-text">
                      <i className="fa fa-image action-image-add-button-icon"></i>
                      <span>{__('Upload an image as your action')}</span>
                    </span>
                  </Link>
                </div>
                <div id="ActionUploadFilePhotoPreview" className="pull-left action-upload-main-image-preview"></div>
                <div id="WrapActionFormName" className="panel-body action-form-panel-body none pull-left action-input-name">
                  <textarea name="name" ref="name" className="form-control change-warning" placeholder={__("Write an action...")}></textarea>
                </div>
                <div className="panel-body action-form-panel-body form-group none" id="WrapCommonActionGoal">
                  <div className="input-group feed-action-goal-select-wrap">
                    <span className="input-group-addon" id=""><i className="fa fa-flag"></i></span>
                    {goal_select}
                  </div>
                </div>
              </form>
              <form action="/setup/ajax_upload_file" id="UploadFileForm" className="upload-file-form none" encType="multipart/form-data" method="post" acceptCharset="utf-8">
                <span className="upload-file-form-message upload-file-form-content none">
                  <i className="fa fa-cloud-upload upload-file-form-content none"></i>
                </span>
              </form>
              <div id="UploadFileAttachButton" className="dz-clickable none"></div>
              <form action="/setup/ajax_remove_file" id="RemoveFileForm" className="none" method="post" acceptCharset="utf-8">
                <input type="hidden" name="data[AttachedFile][file_id]" id="AttachedFileFileId" />
              </form>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
