import React from "react";
import {nl2br} from "~/util/element";
import AttachedFile from "~/message/components/elements/detail/AttachedFile";


export default class Message extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const {topic, message} = this.props
    const read_mark_el = () => {
      if (topic.latest_message_id != message.id) {
        return null;
      }

      const is_all_read = (topic.read_count == topic.members_count - 1);
      if (is_all_read) {
        return (
          <div>
            <a href="#" className="topicDetail-messages-item-read is-on">
              <i className="fa fa-check"/>
            </a>
          </div>
        )
      } else {
        return (
          <div>
            <a href="#" className="topicDetail-messages-item-read is-off">
              <i className="fa fa-check mr_2px"/>
              {topic.read_count}
              <span className="ml_5px topicDetail-messages-item-read-update">{__("Update")}</span>
            </a>
          </div>
        )
      }
    }

    return (
      <div className="topicDetail-messages-item" key={ message.id }>
        <div className="topicDetail-messages-item-left">
          <a href={`/users/view_goals/user_id:${message.user.id}`}
             className="topicDetail-messages-item-left-profileImg">
            <img className="lazy"
                 src={message.user.medium_img_url}/>
          </a>
        </div>
        <div className="topicDetail-messages-item-right">
          <div className>
            <a href="/users/view_goals/user_id:441" className="topicDetail-messages-item-userName">
              <span>{message.user.display_username}</span>
            </a>
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
            return <AttachedFile attached_file={attached_file}/>
          })}
          {read_mark_el()}
        </div>
      </div>
    )
  }
}
Message.propTypes = {
  message: React.PropTypes.object,
};

Message.defaultProps = {
  message: {},
};
