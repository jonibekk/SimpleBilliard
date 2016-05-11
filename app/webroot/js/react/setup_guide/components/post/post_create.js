import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'
import {  } from '../../actions/post_actions'

export default class PostCreate extends React.Component {
  constructor(props) {
    super(props)
  }
  componentWillMount() {
    if(Object.keys(this.props.post.selected_circle).length === 0) {
      browserHistory.push('/setup/post/circle_select')
    }
    this.props.fetchFileUploadFormElement()
  }
  componentDidMount() {
    const html = this.props.post.file_upload_html
    $('.file-upload-form-for-setup').append(html)
    if(this.props.post.selected_circle.public_flg) {
      cake.data.b = this.props.post.selected_circle
    } else {
      cake.data.select2_secret_circle = this.props.post.selected_circle
    }
    initCircleSelect2()
  }
  render() {
    const textareaStyle = {
      overflow: 'hidden',
      resize: "none",
      height: "25px"
    }
    const share_public_input_style = {
      display: this.props.post.selected_circle.public_flg ? 'block' : 'none'
    }
    const share_secret_input_style = {
      display: this.props.post.selected_circle.public_flg ? 'none' : 'block'
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
                  acceptCharset="utf-8"
                  onSubmit={(e) => {this.props.onSubmitPost(e, this.refs)}}>
              <div className="post-panel-body plr_11px ptb_7px">
                <div className="form-group">
                  <textarea name="post_body"
                            ref="post_body"
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
                <div className="col col-xxs-10 col-xs-10 post-share-range-list" id="PostPublicShareInputWrap" style={share_public_input_style}>
                  <input type="hidden" ref="share_public" id="select2PostCircleMember"
                         name="share_public"
                         value={this.props.post.selected_circle.public_flg ? 'circle_' + this.props.post.selected_circle.id : ''} />
                </div>
                <div className="col col-xxs-10 col-xs-10 post-share-range-list" id="PostSecretShareInputWrap" style={share_secret_input_style}>
                  <input type="hidden" id="select2PostSecretCircle" name="share_secret" ref="share_secret"
                         value={this.props.post.selected_circle.public_flg ?  '' : 'circle_' + this.props.post.selected_circle.id} />
                </div>
                <div className="col col-xxs-2 col-xs-2 text-center post-share-range-toggle-button-container">
                  <Link to="#" id="postShareRangeToggleButton" className="btn btn-lightGray btn-white post-share-range-toggle-button" ref="postShareRangeToggleButton">
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
            <div className="file-upload-form-for-setup"></div>
          </div>
        </div>
      </div>
    )
  }
}
