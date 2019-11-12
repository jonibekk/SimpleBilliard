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
  TopicTitleSettingStatus,
  JumpToLatest
} from "~/message/constants/Statuses";
import {isIOSApp} from "~/util/base";

class Body extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      init_scrolled_bottom: false,
      is_scrolled_bottom: false,
      before_scroll_height: 0,
      message_translation_error_shown: false
    };
    this.scrollFunction = this.scrollListener.bind(this);
    this.scrollBottom = this.scrollBottom.bind(this);
    this.onTouchMove = this.onTouchMove.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.fetch_latest_messages_status == FetchLatestMessageStatus.SUCCESS) {
      if (this.isScrolledBottom()) {
        this.setState({is_scrolled_bottom: true});
      }
    }
    if (nextProps.fetch_more_messages_status == FetchMoreMessages.SUCCESS) {
      const el = this._findElement();
      this.setState({before_scroll_height: el.scrollHeight})
    }
    if (this.props.jump_to_latest_status !== nextProps.jump_to_latest_status && nextProps.jump_to_latest_status === JumpToLatest.DONE) {
      this.scrollBottom(true);
    }
  }

  componentDidUpdate() {
    if (this.props.search_message_id && !this.props.is_fetched_search && this.props.paging.new) {
      this.props.dispatch(
        actions.fetchMoreMessages(this.props.paging.new, false)
      );
      return;
    }
    if (this.judgeScrollBottom()) {
      this.scrollBottom();
    } else {
      this.scrollToLastPosition();
    }
    this.resetStatus();
    if (this.state.init_scrolled_bottom) {
      this.attachScrollListener();
    }
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
      || this.props.save_message_status == SaveMessageStatus.ERROR) {
      this.props.dispatch(
        actions.resetSaveMessageStatus()
      )
    }
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.SUCCESS
      || this.props.topic_title_setting_status == TopicTitleSettingStatus.ERROR) {
      this.props.dispatch(
        actions.resetTopicTitleSettingStatus()
      )
    }
    if (this.props.fetch_latest_messages_status == FetchLatestMessageStatus.SUCCESS
      || this.props.fetch_latest_messages_status == FetchLatestMessageStatus.ERROR) {
      this.props.dispatch(
        actions.resetFetchLatestMessagesStatus()
      )
    }
    if (this.props.fetch_more_messages_status == FetchMoreMessages.SUCCESS
      || this.props.fetch_more_messages_status == FetchMoreMessages.ERROR) {
      this.props.dispatch(
        actions.resetFetchMoreMessagesStatus()
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
    if (!this.props.is_old_direction) {
      return;
    }
    if (!this.props.last_position_message_id) {
      return;
    }
    const el = this._findElement();
    // Temporarily stop the inertial scroll to prevent message loading continuously
    el.setAttribute('style', '-webkit-overflow-scrolling: auto;');
    // Scroll last position
    // 古いメッセージが読み込まれてることをわからせるために、読み込んだ古いメッセージが見えるよう-10pxだけ最後の読み込み位置より上に移動
    el.scrollTop = el.scrollHeight - this.state.before_scroll_height - 10;
    el.setAttribute('style', '-webkit-overflow-scrolling: touch;')
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
    const el = this._findElement();
    el.addEventListener('scroll', this.scrollFunction, true);
  }

  scrollListener(e) {
    e.stopPropagation();
    if (this.props.messages.length <= 0) {
      return;
    }
    if (this.props.fetch_more_messages_status == FetchMoreMessages.LOADING) {
      return;
    }

    const el = this._findElement();
    const top_scroll_pos = el.scrollTop;
    const bottom_scroll_pos = el.offsetHeight + top_scroll_pos;
    const threshold = 0;
    const height = el.scrollHeight;


    if (this.props.search_message_id) {
      if (this.props.jump_to_latest_status === JumpToLatest.NONE) {
        this.props.dispatch(
          actions.setJumpToLatestStatus(JumpToLatest.VISIBLE)
        );
      }
      if (this.props.jump_to_latest_status === JumpToLatest.VISIBLE && !this.props.paging.new && bottom_scroll_pos + threshold >= height) {
        this.props.dispatch(
          actions.setJumpToLatestStatus(JumpToLatest.DONE)
        );
      }
    }


    if (this.props.paging.old && top_scroll_pos <= threshold) {
      this.detachScrollListener();
      this.props.dispatch(
        actions.fetchMoreMessages(this.props.paging.old, true)
      );
    } else if (this.props.paging.new && bottom_scroll_pos + threshold >= height) {
      this.detachScrollListener();
      this.props.dispatch(
        actions.fetchMoreMessages(this.props.paging.new, false)
      );
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

  scrollBottom(changeState = true) {
    const el = this._findElement();
    el.scrollTop = el.scrollHeight;

    if (changeState) {
      this.setState({init_scrolled_bottom: true})
    }
  }

  detachScrollListener() {
    const el = this._findElement();
    el.removeEventListener('scroll', this.scrollFunction, true);
    el.removeEventListener('resize', this.scrollFunction, true);
  }

  componentWillUnmount() {
    this.detachScrollListener();
  }

  onTouchMove(e) {
    if (!this.state.init_scrolled_bottom) {
      e.preventDefault()
    }
  }

  jumpToLatest() {
    this.props.dispatch(
      actions.resetMessages()
    );

  }

  render() {
    const {topic, messages, fetch_more_messages_status, is_mobile_app, fetching_read_count, search_message_id, is_old_direction} = this.props;
    const sp_class = this.props.is_mobile_app ? "mod-sp" : "";

    let body_styles = {};
    if (this.props.save_message_status == SaveMessageStatus.SUCCESS) {
      body_styles = { 'paddingBottom' : 0};
    }

    // Render messages
    const renderMessages = () => {
      // Nothing Messages
      if (messages.length == 0) {
        return <Loading/>
      }
      return messages.map((message, i) => {
        return (
          <Message
            topic={topic}
            is_active={search_message_id == message.id}
            ref={`message_${message.id}`}
            message={message}
            key={message.id}
            fetching_read_count={fetching_read_count}
            message_translation_active={this.props.message_translation_active}
          />
        )
      });
    };
    return (
      <div className={`topicDetail-body ${sp_class} ${isIOSApp() ? 'mod-ios-app' : ''}`} ref="topic_detail_body" style={body_styles} >
        <div className={`topicDetail-body-inner ${sp_class}`}  ref="messages"
             onTouchMove={this.onTouchMove}>
          {(fetch_more_messages_status == FetchMoreMessages.LOADING && is_old_direction) && <Loading/>}
          {renderMessages()}
          {(fetch_more_messages_status == FetchMoreMessages.LOADING && !is_old_direction) && <Loading/>}
        </div>
        <div
          className={`topicDetail-jumpToLatest ${this.props.jump_to_latest_status === JumpToLatest.VISIBLE ? 'is-show' : ''}`}
          onClick={this.jumpToLatest.bind(this)}>
          <i className="fa fa-arrow-circle-down"></i>
          <span>{__("View the latest messages")}</span>
        </div>
      </div>
    )
  }
}

Body.propTypes = {
  topic: React.PropTypes.object,
  search_message_id: React.PropTypes.number,
  fetch_more_messages_status: React.PropTypes.number,
  jump_to_latest_status: React.PropTypes.number,
  messages: React.PropTypes.array,
  paging: React.PropTypes.object,
  is_fetched_initial: React.PropTypes.bool,
  is_mobile_app: React.PropTypes.bool,
  fetching_read_count: React.PropTypes.bool,
  is_fetched_search: React.PropTypes.bool,
  message_translation_active: React.PropTypes.bool
};

Body.defaultProps = {
  topic: {},
  search_message_id: null,
  fetch_more_messages_status: FetchMoreMessages.NONE,
  fetch_latest_messages_status: FetchLatestMessageStatus.NONE,
  jump_to_latest_status: JumpToLatest.NONE,
  messages: [],
  paging: {old: "", new: ""},
  is_fetched_initial: false,
  is_mobile_app: false,
  fetching_read_count: false,
  is_fetched_search: false,
  message_translation_active: false
};
export default connect()(Body);
