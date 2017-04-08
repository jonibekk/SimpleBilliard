import React from "react";
import ReactDOM from "react-dom";
import {connect} from "react-redux";
import * as actions from "~/message/actions/detail";
import Message from "~/message/components/elements/detail/Message";
import Loading from "~/message/components/elements/detail/Loading";
import {
  FetchLatestMessageStatus,
  FetchMoreMessages,
  SaveMessageStatus,
  TopicTitleSettingStatus
} from "~/message/constants/Statuses";
import {isIOSApp} from "~/util/base";
import {PositionIOSApp} from "~/message/constants/Styles";

class Body extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      init_scrolled_bottom: false,
      is_scrolled_bottom: false,
    }
    this.scrollFunction = this.scrollListener.bind(this);
    this.scrollBottom = this.scrollBottom.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.fetch_latest_messages_status == FetchLatestMessageStatus.SUCCESS) {
      if (this.isScrolledBottom()) {
        this.setState({is_scrolled_bottom: true});
      }
    }
  }

  componentDidUpdate() {
    this.scrollBottom();
    this.scrollToLastPosition();
    this.resetStatus();
    this.attachScrollListener();
  }

  isScrolledBottom() {
    let el = this._findElement();
    if ((el.offsetHeight + el.scrollTop) < el.scrollHeight) {
      return false;
    }
    return true;
  }

  resetStatus() {
    if (this.props.save_message_status == SaveMessageStatus.SUCCESS
      || this.props.save_message_status == SaveMessageStatus.ERROR)
    {
      this.props.dispatch(
        actions.resetSaveMessageStatus()
      )
    }
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.SUCCESS
      || this.props.topic_title_setting_status == TopicTitleSettingStatus.ERROR)
    {
      this.props.dispatch(
        actions.resetTopicTitleSettingStatus()
      )
    }
    if (this.props.fetch_latest_messages_status == FetchLatestMessageStatus.SUCCESS
      || this.props.fetch_latest_messages_status == FetchLatestMessageStatus.ERROR)
    {
      this.props.dispatch(
        actions.resetFetchLatestMessagesStatus()
      )
    }
  }

  /**
   * After load old messages, scroll to last position
   */
  scrollToLastPosition() {
    if (this.props.fetch_more_messages_status !== FetchMoreMessages.SUCCESS) {
      return;
    }
    if (!this.props.last_position_message_id) {
      return;
    }

    const node = ReactDOM.findDOMNode(this.refs['message_' + this.props.last_position_message_id]);
    if (node) {
      node.scrollIntoView();
      this.props.dispatch(
        actions.resetFetchMoreMessagesStatus()
      )
    }
  }

  _findElement() {
    return ReactDOM.findDOMNode(this.refs.messages);
  }

  // TODO: componentize
  attachScrollListener() {
    if (!this.props.is_fetched_initial) {
      return;
    }
    if (!this.state.init_scrolled_bottom) {
      return;
    }
    let el = this._findElement();
    el.addEventListener('scroll', this.scrollFunction, true);
    el.addEventListener('resize', this.scrollFunction, true);
  }

  scrollListener() {
    if (this.props.messages.length <= 0) {
      return;
    }
    if (!this.props.paging.next) {
      return;
    }
    if (this.props.fetch_more_messages_status == FetchMoreMessages.LOADING) {
      return;
    }

    let el = this._findElement();
    let top_scroll_pos = el.scrollTop;
    const threshold = 300;
    if (top_scroll_pos < threshold) {
      this.detachScrollListener();
      this.props.dispatch(
        actions.fetchMoreMessages(this.props.paging.next)
      )
    }
  }

  judgeScrollBottom() {
    if (this.props.messages.length <= 0) {
      return false;
    }
    if (this.props.is_fetched_initial && !this.state.init_scrolled_bottom) {
      return true;
    }
    if (this.props.save_message_status == SaveMessageStatus.SUCCESS) {
      return true;
    }
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.SUCCESS) {
      return true;
    }
    if (this.props.fetch_latest_messages_status == FetchLatestMessageStatus.SUCCESS) {
      if (this.state.is_scrolled_bottom) {
        this.setState({is_scrolled_bottom: false});
        return true;
      }
    }
    return false;
  }

  scrollBottom() {
    if (!this.judgeScrollBottom()) {
      return;
    }

    let el = this._findElement();
    el.scrollTop = el.scrollHeight;

    this.setState({init_scrolled_bottom: true})
  }

  detachScrollListener() {
    let el = this._findElement();
    el.removeEventListener('scroll', this.scrollFunction, true);
    el.removeEventListener('resize', this.scrollFunction, true);
  }

  componentWillUnmount() {
    this.detachScrollListener();
  }

  render() {
    const {topic, messages, fetch_more_messages_status, is_mobile_app} = this.props

    const sp_class = this.props.is_mobile_app ? "mod-sp" : "";

    const body_styles = {
      top: this.props.mobile_app_layout.body_top,
      bottom: this.props.mobile_app_layout.body_bottom
    };

    // Nothing Messages
    if (messages.length == 0) {
      return (
        <div className="topicDetail-body">
          <div className={`topicDetail-messages ${sp_class}`} ref="messages" style={body_styles}>
            <Loading/>
          </div>
        </div>
      )
    }

    // Exist Messages
    const messages_el = messages.map((message, i) => {
      return (
        <Message
          topic={topic}
          ref={`message_${message.id}`}
          message={message}
          key={message.id}
        />
      )
    });

    return (
      <div className="topicDetail-body">
        <div className={`topicDetail-messages ${sp_class}`} ref="messages" style={body_styles}>
          {messages_el}
          {(fetch_more_messages_status == FetchMoreMessages.LOADING) && <Loading/>}
        </div>
      </div>
    )
  }
}

Body.propTypes = {
  topic: React.PropTypes.object,
  fetch_more_messages_status: React.PropTypes.number,
  messages: React.PropTypes.array,
  paging: React.PropTypes.object,
  is_fetched_initial: React.PropTypes.bool,
  is_mobile_app: React.PropTypes.bool,
};

Body.defaultProps = {
  topic: {},
  fetch_more_messages_status: FetchMoreMessages.NONE,
  fetch_latest_messages_status: FetchLatestMessageStatus.NONE,
  messages: [],
  paging: {next: ""},
  is_fetched_initial: false,
  is_mobile_app: false,
};
export default connect()(Body);
