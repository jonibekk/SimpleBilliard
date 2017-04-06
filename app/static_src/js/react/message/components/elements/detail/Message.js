import React from "react";
import { connect } from "react-redux"
import {nl2br} from "~/util/element";
import AttachedFile from "~/message/components/elements/detail/AttachedFile";
import * as Model from "~/common/constants/Model";
import { fetchReadCount } from '~/message/actions/detail'

class Message extends React.Component {
  constructor(props) {
    super(props);
  }

  onClickReadCount() {
    const { topic, dispatch } = this.props
    dispatch(fetchReadCount(topic.id))
  }

  render() {
    const {topic, message, is_first_idx} = this.props
    const read_mark_el = () => {
      if (topic.latest_message_id != message.id) {
        return null;
      }

      const is_all_read = (topic.read_count == topic.members_count - 1);
      if (is_all_read) {
        return (
          <div>
            <a href={`/topics/ajax_get_read_members/${topic.id}`}
               className="topicDetail-messages-item-read is-on modal-ajax-get">
              <i className="fa fa-check"/>
            </a>
          </div>
        )
      } else {
        return (
          <div className="topicDetail-messages-item-read-wrapper">
            <a href={`/topics/ajax_get_read_members/${topic.id}`}
               className="topicDetail-messages-item-read is-off modal-ajax-get"
               onClick={ this.onClickReadCount.bind(this) }>
              <i className="fa fa-check mr_2px"/>
              {topic.read_count}
              <span className="ml_5px topicDetail-messages-item-read-update">{__("Update")}</span>
            </a>
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
          <p className="topicDetail-messages-item-content">
            {message.body === "[like]" ?
              <i className="fa fa-thumbs-o-up font_brownRed"></i>
              : nl2br(message.body)
            }
          </p>
          {message.attached_files.map((attached_file) => {
            return (
              <AttachedFile
                key={attached_file.id}
                attached_file={attached_file}
                message_id={message.id}/>
            )
          })}
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
