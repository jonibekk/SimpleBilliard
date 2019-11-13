import React from "react";
import {connect} from "react-redux";
import Linkfy from "react-linkify";
import {nl2br} from "~/util/element";
import AttachedFile from "~/message/components/elements/detail/AttachedFile";
import Loading from "~/message/components/elements/detail/Loading";
import * as Model from "~/common/constants/Model";
import {fetchReadCount} from "~/message/actions/detail";
import {translateMessage} from "../../../modules/translation";
import Noty from "noty";

class Message extends React.Component {

  constructor(props) {
    super(props);

    this.updateMessageForTranslation = this.updateMessageForTranslation.bind(this);

    this.state = {
      message_body: this.props.message.body
    };
  }

  onClickReadCount() {
    const {topic, dispatch} = this.props
    dispatch(fetchReadCount(topic.id))
  }

  componentWillReceiveProps(nextProps, nextContext) {
    if (nextProps.message_translation_active !== this.props.message_translation_active) {
      if (nextProps.message_translation_active) {
        this.updateMessageForTranslation();
      } else {
        this.setState({message_body: this.props.message.body});
      }
    }
  }

  componentDidMount() {
    if (this.props.message_translation_active) {
      this.updateMessageForTranslation();
    } else {
      this.setState({message_body: this.props.message.body});
    }
  }

  updateMessageForTranslation() {
    // Skip system messages (topic change, member change, etc.)
    if (this.props.message.type !== '1') {
      return;
    }
    translateMessage(this.props.message.id)
      .then((result) => {
        if (this.props.message_translation_active && result !== undefined && result.length > 0) {
          this.setState({message_body: result});
        } else {
          this.setState({message_body: this.props.message.body});
        }
      })
      .catch(error => {
        // Ignore client cancelled request error
        if (error.code !== 'ECONNABORTED') {
          new Noty({
            type: 'error',
            text: cake.word.message_translation_error,
            timeout: 3000,
            killer: true
          }).show();
        }
        this.setState({message_body: this.props.message.body});
      });
  }

  render() {
    const {topic, message, fetching_read_count, is_active} = this.props;

    const read_mark_el = () => {
      if (topic.latest_message_id != message.id) {
        return null;
      }

      const is_all_read = (topic.read_count == topic.members_count - 1);
      if (is_all_read) {
        return (
          <div className="topicDetail-messages-item-read-wrapper">
            <a href="#"
               data-url={`/topics/ajax_get_read_members/${topic.id}`}
               className="topicDetail-messages-item-read is-on modal-ajax-get">
              <i className="fa fa-check"/>
            </a>
          </div>
        )
      } else {
        return (
          <div className="topicDetail-messages-item-read-wrapper">
            <div className="topicDetail-messages-item-read is-off">
              {fetching_read_count && <Loading size={12}/>}
              <a href="#"
                 data-url={`/topics/ajax_get_read_members/${topic.id}`}
                 className={`topicDetail-messages-item-read-link modal-ajax-get ${fetching_read_count ? 'is-loading' : ''}`}
                 onClick={this.onClickReadCount.bind(this)}>
                <i className="fa fa-check mr_2px"/>
                <span className={`topicDetail-messages-item-read-link-number`}>{topic.read_count}</span>
              </a>
            </div>
            <div className="topicDetail-messages-item-update">
              <a className="topicDetail-messages-item-update-link"
                 onClick={this.onClickReadCount.bind(this)}>
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
        <div className="topicDetail-messages-item mod-sysInfo is_active">
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
      <div className={`topicDetail-messages-item ${is_active ? 'is-active' : ''}`}>
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
            {nl2br(this.state.message_body)}
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
  is_active: React.PropTypes.bool,
  message_translation_active: React.PropTypes.bool
};

Message.defaultProps = {
  topic: {},
  message: {},
  is_active: false,
  message_translation_active: false
};

export default connect()(Message);
