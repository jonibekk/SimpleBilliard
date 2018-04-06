import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class PostCreate extends React.Component {
  constructor(props) {
    super(props)
  }
  componentWillMount() {
    if(Object.keys(this.props.post.selected_circle).length === 0) {
      browserHistory.push('/setup/post/circle_select')
    }
    let pusher = new Pusher(cake.pusher.key)
    pusher.connection.bind('connected', function () {
        cake.pusher.socket_id = pusher.connection.socket_id
    });
  }
  componentDidMount() {
  }
  render() {
    const textareaStyle = {
      overflow: 'hidden',
      resize: "none",
      height: "25px",
      "wordWrap":"break-word"
    }
    const share_public_value = () => {
      let val = ''
      if (this.props.post.selected_circle.team_all_flg) {
        val = 'public'
      } else if (this.props.post.selected_circle.public_flg) {
        val = 'circle_' + this.props.post.selected_circle.id
      }
      return val
    }
    const validation_errors = this.props.post.validation_errors.map((error) => {
      <small className="help-block">{error[0]}</small>
    })
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right"></i> {__('Post to a circle')}
        </div>
        {validation_errors}
        <div className="panel panel-default global-form setup-post-form" id="GlobalForms">
          <div className="tab-pane active" id="PostForm">
            <form id="PostDisplayForm"
                  className="form-feed-notify bv-form"
                  encType="multipart/form-data"
                  method="post"
                  acceptCharset="utf-8"
                  onSubmit={(e) => {this.props.onSubmitPost(e, this.refs, cake.pusher.socket_id)}}>
              <input type="hidden" ref="share_public"
                     value={share_public_value()} />
              <input type="hidden" ref="share_secret"
                     value={this.props.post.selected_circle.public_flg ?  '' : 'circle_' + this.props.post.selected_circle.id} />
              <input type="hidden" ref="share_range"
                    value={this.props.post.selected_circle.public_flg ? 'public' : 'secret'} />
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
                            style={textareaStyle}
                            maxLength="10000"
                            onChange={() => {this.props.toggleButtonClickable(this.refs)}}></textarea>
                </div>
                <div id="PostUploadFilePreview" className="post-upload-file-preview"></div>
              </div>
              <div className="panel-body post-share-range-panel-body" id="PostFormShare">
                {this.props.post.selected_circle.name}
              </div>
              <div className="post-panel-footer">
                <div className="font_12px" id="PostFormFooter">
                  <a href="#" className="link-red" id="PostUploadFileButton">
                    <button type="button" className="btn pull-left btn-photo-up"><i
                            className="fa fa-paperclip post-camera-icon"></i>
                    </button>
                  </a>
                  <div className="row form-horizontal form-group post-share-range" id="PostShare">
                    <div className="submit">
                      <input className="btn btn-primary pull-right post-submit-button" id="PostSubmit" type="submit" value={__("Post")}
                      disabled={!Boolean(this.props.post.can_click_submit_button)} />
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div>
          <Link className="btn btn-secondary setup-back-btn-full"
                to={this.props.post.circles.length ? "/setup/post/circle_select" : "/setup/post/image"} >
            {__('Back')}
          </Link>
        </div>
      </div>
    )
  }
}
