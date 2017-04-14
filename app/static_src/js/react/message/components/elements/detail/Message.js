import React from "react";
import {connect} from "react-redux";
import Linkfy from "react-linkify";
import {nl2br} from "~/util/element";
import AttachedFile from "~/message/components/elements/detail/AttachedFile";
import Loading from "~/message/components/elements/detail/Loading";
import * as Model from "~/common/constants/Model";
import {fetchReadCount} from "~/message/actions/detail";

class Message extends React.Component {
  constructor(props) {
    super(props);
  }

  onClickReadCount() {
    const {topic, dispatch} = this.props
    dispatch(fetchReadCount(topic.id))
  }

  render() {
    const {topic, message, fetch_read_count} = this.props
    const read_mark_el = () => {
      if (topic.latest_message_id != message.id) {
        return null;
      }

      const is_all_read = (topic.read_count == topic.members_count - 1);
      if (is_all_read) {
        return (
          <div className="topicDetail-messages-item-read-wrapper">
            <a href={`/topics/ajax_get_read_members/${topic.id}`}
               className="topicDetail-messages-item-read is-on modal-ajax-get">
              <i className="fa fa-check"/>
            </a>
          </div>
        )
      } else {
        return (
          <div className="topicDetail-messages-item-read-wrapper">
            <div className="topicDetail-messages-item-read is-off">
              {fetch_read_count && <Loading size={12} />}
              <a href={`/topics/ajax_get_read_members/${topic.id}`}
                 className={`topicDetail-messages-item-read-link modal-ajax-get ${fetch_read_count ? 'is-loading' : ''}`}
                 onClick={ this.onClickReadCount.bind(this) }>
                <i className="fa fa-check mr_2px"/>
                <span className={`topicDetail-messages-item-read-link-number`}>{topic.read_count}</span>
              </a>
            </div>
            <div className="topicDetail-messages-item-update">
              <a className="topicDetail-messages-item-update-link"
                 onClick={ this.onClickReadCount.bind(this) }>
                <span className="ml_5px topicDetail-messages-item-read-update">{__("Update")}</span>
              </a>
            </div>
          </div>
        )
      }
    }

    // System info message (Add members, etc)
    if (message.type != Model.Message.TYPE_NORMAL) {
      return (
        <div className="topicDetail-messages-item mod-sysInfo">
          <p className="topicDetail-messages-item-onlyText">
            {nl2br(message.body)}
          </p>
        </div>
      )
    }

    const attached_files = () => {
      if (message.attached_files.length == 0) {
        return null;
      }

      const files = message.attached_files.map((attached_file) => {
        return (
          <AttachedFile
            key={attached_file.id}
            attached_file={attached_file}
            message_id={message.id}/>
        )
      })
      return (
        <div className="topicDetail-messages-item-attachedFiles">
          {files}
        </div>
      )
    }

    return (
      <div className={`topicDetail-messages-item`}>
        <div className="topicDetail-messages-item-left">
          <a href={`/users/view_goals/user_id:${message.user.id}`}
             className="topicDetail-messages-item-left-profileImg">
            <img className="lazy"
                 src={message.user.medium_img_url}/>
          </a>
        </div>
        <div className="topicDetail-messages-item-right">
          <div className>
            <span className="topicDetail-messages-item-userName">
              {message.user.display_username}
            </span>
            <span className="topicDetail-messages-item-datetime">
              {message.display_created}
            </span>
          </div>
          <Linkfy className="topicDetail-messages-item-content" properties={{target: '_blank'}}>
            {nl2br(message.body)}
          </Linkfy>
          {attached_files()}
          {read_mark_el()}
        </div>
      </div>
    )
  }
}
Message.propTypes = {
  topic: React.PropTypes.object,
  message: React.PropTypes.object,
};

Message.defaultProps = {
  topic: {},
  message: {},
};

export default connect()(Message);
