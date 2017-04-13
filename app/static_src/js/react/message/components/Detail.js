import React from "react";
import {browserHistory} from "react-router";
import Header from "~/message/components/elements/detail/Header";
import Body from "~/message/components/elements/detail/Body";
import Footer from "~/message/components/elements/detail/Footer";
import {isMobileApp} from "~/util/base";
import Base from "~/common/components/Base";

export default class Detail extends Base {
  constructor(props) {
    super(props);
    this.fetchLatestMessages = this.fetchLatestMessages.bind(this);
  }

  componentWillMount() {
    // Set resource ID included in url.
    const topic_id = this.props.params.topic_id;
    this.props.setResourceId(topic_id);
    this.props.setUaInfo();
    this.props.initLayout();
    this.props.fetchInitialData(this.props.params.topic_id);

    if (isMobileApp()) {
      let header = document.getElementById("header");
      header.classList.add("mod-shadow");
    }
  }

  componentDidMount() {
    super.componentDidMount.apply(this);
    const topic_id = this.props.params.topic_id;
    let {pusher_info} = this.props.detail;
    // HACK:dependencied to window.Pusher(using in gl_basic.js)
    const self = this;
    $(document).ready(function () {
      let pusher = pusher_info.pusher ? pusher_info.pusher : new window.Pusher(cake.pusher.key);
      // Set socket_id
      pusher.connection.bind('connected', function () {
        const socket_id = pusher.connection.socket_id;
        self.props.setPusherInfo({socket_id});
      });
      // Subscribe
      let channel = pusher.subscribe(`message-channel-${topic_id}`);
      channel.bind('new_message', self.fetchLatestMessages);
      self.props.setPusherInfo({pusher, channel})
    });
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.detail.redirect) {
      browserHistory.push("/topics");
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this);
    if (isMobileApp()) {
      let header = document.getElementById("header");
      header.classList.remove("mod-shadow");
    }

    this.props.resetStates();
    // Unsubscribe
    let {channel} = this.props.detail.pusher_info;
    channel.unbind('new_message', self.fetchLatestMessages);
  }

  fetchLatestMessages() {
    const messages = this.props.detail.messages.data;
    const latest_message_id = messages[messages.length - 1].id;
    this.props.fetchLatestMessages(latest_message_id);
  }

  render() {
    const {detail, file_upload} = this.props;
    return (
      <div className={`topicDetail ${isMobileApp() ? "" : "panel panel-default"}`}>
        <Header
          topic={detail.topic}
          topic_title_setting_status={detail.topic_title_setting_status}
          save_topic_title_err_msg={detail.save_topic_title_err_msg}
          is_mobile_app={detail.is_mobile_app}
          mobile_app_layout={detail.mobile_app_layout}
          leave_topic_status={detail.leave_topic_status}
          leave_topic_err_msg={detail.leave_topic_err_msg}
        />
        <Body
          topic={detail.topic}
          messages={detail.messages.data}
          paging={detail.messages.paging}
          is_fetched_initial={detail.is_fetched_initial}
          fetch_more_messages_status={detail.fetch_more_messages_status}
          fetch_latest_messages_status={detail.fetch_latest_messages_status}
          last_position_message_id={detail.last_position_message_id}
          save_message_status={detail.save_message_status}
          topic_title_setting_status={detail.topic_title_setting_status}
          is_mobile_app={detail.is_mobile_app}
          mobile_app_layout={detail.mobile_app_layout}
        />
        <Footer
          body={detail.input_data.body}
          save_message_status={detail.save_message_status}
          is_mobile_app={detail.is_mobile_app}
          err_msg={detail.err_msg}
          mobile_app_layout={detail.mobile_app_layout}
          {...file_upload}
        />
      </div>
    )
  }
}
