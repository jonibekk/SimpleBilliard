import React from "react";
import ReactDom from "react-dom";
import {browserHistory, Link} from "react-router";
import UploadDropZone from "~/message/components/elements/detail/UploadDropZone";
import UploadPreview from "~/message/components/elements/detail/UploadPreview";
import LoadingButton from "~/message/components/elements/ui_parts/LoadingButton";
import {nl2br} from "~/util/element";
import {isMobileApp, disableAsyncEvents, isIOSApp} from "~/util/base";
import Base from "~/common/components/Base";
import Textarea from "react-textarea-autosize";

export default class TopicCreate extends Base {

  constructor(props) {
    super(props)
    // HACK:Display drop zone when dragging
    // reference:http://qiita.com/sounisi5011/items/dc4878d3e8b38101cf0b
    this.state = Object.assign({}, this.state, {
      is_drag_over: false,
      is_drag_start: false,
    })
  }

  componentDidMount() {
    super.componentDidMount.apply(this);
    // HACK: merge componentDidMount in parent Base.js
    window.addEventListener("beforeunload", this.onBeforeUnloadSelect2Handler.bind(this))
    // enable `routerWillLeave` method
    this.props.router.setRouteLeaveHook(this.props.route, this.routerWillLeave.bind(this));
    disableAsyncEvents()

    // HACK:To use select2Member
    //      Now, initialize select2 by initMemberSelect2 in gl_basic
    // Set selectd user
    const user_id = this.props.location.query.user_id
    if (user_id) {
      ReactDom.findDOMNode(this.refs.select2Member).value = `user_${user_id}`
      this.changeToUserIds()
    }
    // HACK: メンバー選択処理初期化
    // SPAの為トピックリストから遷移した時はinitMemberSelect2は呼ばれないので、もう一度呼ぶ必要がある。
    $(document).ready(function (e) {
      if ($("#s2id_select2Member").length == 0) {
        initMemberSelect2();
      }
    });
  }

  onBeforeUnloadSelect2Handler(event) {
    if (this.getToUserIdsByDom().length > 0) {
      return event.returnValue = this.state.leave_page_alert_msg
    }
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.topic_create.input_data.body == "" && nextProps.file_upload.uploaded_file_ids.length == 0) {
      this.setState({enabled_leave_page_alert: false})
    } else {
      this.setState({enabled_leave_page_alert: true})
    }

    if (nextProps.topic_create.redirect) {
      browserHistory.push(`/topics/${nextProps.topic_create.new_topic_id}/detail`);
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this);
    window.removeEventListener("beforeunload", this.onBeforeUnloadSelect2Handler.bind(this))
    this.props.resetStates();
  }

  routerWillLeave(nextLocation) {
    if (!this.props.topic_create.is_saving && (this.state.enabled_leave_page_alert || this.getToUserIdsByDom().length > 0)) {
      return this.state.leave_page_alert_msg
    }
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
    const target_input = ReactDom.findDOMNode(this.refs.select2Member);
    if (!target_input) {
      return [];
    }

    let to_user_ids_str = target_input.value;
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
            <input type="hidden" id="select2Member" className="js-changeSelect2Member disable-change-warning"
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
              <Textarea className="topicCreateForm-msgBody-form disable-change-warning"
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
                {/* For avoiding ios bug, only in ios app, not setting multiple attr
                    ref) https://jira.goalous.com/browse/GL-5732 */}
                <input type="file" multiple={isIOSApp() ? '' : 'multiple'} className="hidden" ref="file" onChange={this.changeFile.bind(this)}/>
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