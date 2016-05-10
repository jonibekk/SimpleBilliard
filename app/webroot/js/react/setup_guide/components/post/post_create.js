import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'

export default class PostCreate extends React.Component {
  constructor(props) {
    super(props)
  }
  render() {
    const textareaStyle = {
      overflow: 'hidden',
      "resize": "none",
      "height": "25px"
    }
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right"></i> {__('Post to a circle')}
        </div>
        <div className="panel panel-default global-form" id="GlobalForms">
          <div className="post-panel-heading ptb_7px plr_11px">
            <ul className="feed-switch clearfix plr_0px" id="CommonFormTabs">
              <li className="switch-action">
                <Link to="#PostForm"
                   className="switch-post-anchor click-target-focus">
                   <i className="fa fa-comment-o"></i>{__("Posts")}
                </Link>
                <span className="switch-arrow"></span>
              </li>
            </ul>
          </div>
          <div className="tab-pane active" id="PostForm">
            <form action="/posts/add"
                  id="PostDisplayForm"
                  className="form-feed-notify bv-form"
                  encType="multipart/form-data"
                  method="post"
                  acceptCharset="utf-8">
              <button type="submit" className="bv-hidden-submit none"></button>
              <div className="post-panel-body plr_11px ptb_7px">
                <div className="form-group">
                  <textarea name="post_body"
                            className="form-control tiny-form-text-change post-form feed-post-form box-align change-warning"
                            id="CommonPostBody"
                            rows="1"
                            cols="30"
                            placeholder={__("Write something...")}
                            required="required"
                            wordWrap="break-word"
                            style={textareaStyle}></textarea>
                </div>
                <div id="PostUploadFilePreview" className="post-upload-file-preview"></div>
              </div>
              <div className="panel-body post-share-range-panel-body" id="PostFormShare">
                <div className="col col-xxs-10 col-xs-10 post-share-range-list" id="PostPublicShareInputWrap">
                  <input type="hidden" id="select2PostCircleMember" name="share_public" />
                </div>
                <div className="col col-xxs-10 col-xs-10 post-share-range-list" id="PostSecretShareInputWrap">
                  <input type="hidden" id="select2PostSecretCircle" name="share_secret" />
                </div>
                <div className="col col-xxs-2 col-xs-2 text-center post-share-range-toggle-button-container">
                  <Link to="#" className="btn btn-lightGray btn-white post-share-range-toggle-button" data-toggle-enabled="">
                    <input type="hidden" id="postShareRange" name="share_range" value="value" />
                  </Link>
                </div>
              </div>
              <div className="post-panel-footer">
                <div className="font_12px" id="PostFormFooter">
                  <a href="#" className="link-red" id="PostUploadFileButton">
                    <button type="button" className="btn pull-left photo-up-btn">
                      <i className="fa fa-paperclip post-camera-icon"></i>
                    </button>
                  </a>
                  <div className="row form-horizontal form-group post-share-range" id="PostShare">
                    <div className="submit">
                      <input className="btn btn-primary pull-right post-submit-button" id="PostSubmit" type="submit" value="Post" />
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    )
  }
}
