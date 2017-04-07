import React from "react";
import ReactDom from "react-dom";
import {browserHistory, Link} from "react-router";
import UploadDropZone from "~/message/components/elements/detail/UploadDropZone";
import UploadPreview from "~/message/components/elements/detail/UploadPreview";
import LoadingButton from "~/message/components/elements/ui_parts/LoadingButton";
import {nl2br} from "~/util/element";
import {isMobileApp} from "~/util/base";
import Base from "~/common/components/Base";

export default class TopicCreate extends Base {

  constructor(props) {
    super(props)
    // HACK:Display drop zone when dragging
    // reference:http://qiita.com/sounisi5011/items/dc4878d3e8b38101cf0b
    this.state = {
      is_drag_over: false,
      is_drag_start: false,
    }
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.topic_create.redirect) {
      browserHistory.push(`/topics/${nextProps.topic_create.new_topic_id}/detail`);
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this);
    this.props.resetStates();
  }


  componentDidMount() {
    super.componentDidMount.apply(this);
    // HACK:To use select2Member
    $(document).ready(function (e) {
      initMemberSelect2();
    });
  }

  createTopic(e) {
    this.props.createTopic();
  }

  uploadFiles(files) {
    if (!files) {
      return;
    }

    this.props.uploadFiles(files)
  }

  dragEnter(e) {
    e.stopPropagation();
    e.preventDefault();
    this.setState({is_drag_start: true});
  }

  dragOver(e) {
    e.stopPropagation();
    e.preventDefault();
    this.setState({is_drag_over: true});
  }

  dragLeave(e) {
    if (this.state.is_drag_start) {
      this.setState({is_drag_start: false});
    } else {
      this.setState({is_drag_over: false});
    }
  }

  drop(e) {
    e.stopPropagation();
    e.preventDefault();

    this.setState({is_drag_over: false});

    const files = e.dataTransfer.files;

    this.uploadFiles(files);
  }

  selectFile(e) {
    ReactDom.findDOMNode(this.refs.file).click();
  }

  changeFile(e) {
    const files = e.target.files;
    this.uploadFiles(files);
  }

  changeToUserIds(e) {
    const to_user_ids = this.getToUserIdsByDom();
    this.props.updateInputData({to_user_ids});
  }

  getToUserIdsByDom() {
    let to_user_ids_str = ReactDom.findDOMNode(this.refs.select2Member).value;
    if (!to_user_ids_str) {
      return [];
    }
    to_user_ids_str = to_user_ids_str.replace(/user_/g, '');
    return to_user_ids_str.split(',');
  }

  changeMessage(e) {
    this.props.updateInputData({body: e.target.value})
  }

  render() {
    const {topic_create, file_upload} = this.props;

    const disableSend = () => {
      if (topic_create.input_data.to_user_ids.length == 0) {
        return true;
      }
      if (!topic_create.input_data.body && file_upload.uploaded_file_ids.length == 0) {
        return true;
      }
      return false;
    }

    return (
      <div className={isMobileApp() ? "p_10px" : ""}>
        <div className="panel topicCreateForm">
          <span className="hidden js-triggerUpdateToUserIds" onClick={this.changeToUserIds.bind(this)}/>
          <div className="topicCreateForm-selectTo ">
            <span className="topicCreateForm-selectTo-label">To:</span>
            <input type="hidden" id="select2Member" className="js-changeSelect2Member"
                   style={{width: '85%'}} ref="select2Member"/>
          </div>
          <div className={this.state.is_drag_over && "uploadFileForm-wrapper"}
               onDrop={this.drop.bind(this)}
               onDragEnter={this.dragEnter.bind(this)}
               onDragOver={this.dragOver.bind(this)}
               onDragLeave={this.dragLeave.bind(this)}
          >
            {this.state.is_drag_over && <UploadDropZone/>}

            <div className="topicCreateForm-msgBody">
                <textarea className="topicCreateForm-msgBody-form"
                          placeholder={__("Write a message...")}
                          defaultValue=""
                          onChange={this.changeMessage.bind(this)}
                />
              <UploadPreview files={file_upload.preview_files}/>
            </div>
            <div className="topicCreateForm-footer">
              <div className="topicCreateForm-footer-left">
                <span
                  className="btn btnRadiusOnlyIcon mod-upload"
                  onClick={this.selectFile.bind(this)}
                />
                <input type="file" className="hidden" ref="file" onChange={this.changeFile.bind(this)}/>
              </div>

              <div className="topicCreateForm-footer-center">
                {topic_create.err_msg &&
                <div className="has-error">
                  <p className="has-error help-block">
                    {nl2br(topic_create.err_msg)}
                  </p>
                </div>
                }
              </div>
              <div className="topicCreateForm-footer-right">
                {(() => {
                  if (topic_create.is_saving) {
                    return <LoadingButton/>
                  } else {
                    return (
                      <span
                        className="btn btnRadiusOnlyIcon mod-send"
                        onClick={this.createTopic.bind(this)}
                        disabled={disableSend() && "disabled"}
                      />
                    )
                  }
                })(this)}
              </div>
            </div>
          </div>
        </div>
        <div className="text-align_r">
          <Link to="/topics" className="btn btn-link design-cancel">
            {__("Cancel")}
          </Link>
        </div>
      </div>
    )
  }
}
