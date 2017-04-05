import React from "react";
import ReactDOM from "react-dom";
import {connect} from "react-redux";
import * as actions from "~/message/actions/detail";
import Message from "~/message/components/elements/detail/Message";
import Loading from "~/message/components/elements/detail/Loading";
import {FetchMoreMessages, SaveMessageStatus} from "~/message/constants/Statuses";

class Body extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      scrolled_bottom: false
    }
    this.scrollFunction = this.scrollListener.bind(this);
    this.scrollBottom = this.scrollBottom.bind(this);
  }

  componentDidUpdate() {
    if (this.props.is_fetched_initial && !this.state.scrolled_bottom) {
      this.scrollBottom();
    }
    if (this.props.save_message_status == SaveMessageStatus.SUCCESS) {
      this.scrollBottom();
      this.props.dispatch(
        actions.resetSaveMessageStatus()
      )
    }

    this.scrollToLastPosition();
    this.attachScrollListener();
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
    if (!this.state.scrolled_bottom) {
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

  scrollBottom() {
    if (this.props.messages.length <= 0) {
      return;
    }

    let el = this._findElement();
    el.scrollTop = el.scrollHeight;

    this.setState({scrolled_bottom: true})
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

    // Nothing Messages
    if (messages.length == 0) {
      return (
        <div className="topicDetail-body">
          <div className={`topicDetail-messages ${sp_class}`} ref="messages">
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
        <div className={`topicDetail-messages ${sp_class}`} ref="messages">
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
  messages: [],
  paging: {next: ""},
  is_fetched_initial: false,
  is_mobile_app: false,
};
export default connect()(Body);
