import React from "react";
import {browserHistory} from "react-router";
import Header from "~/message/components/elements/detail/Header";
import Body from "~/message/components/elements/detail/Body";
import Footer from "~/message/components/elements/detail/Footer";
import Base from "~/common/components/Base";
import {isMobileApp, disableAsyncEvents} from "~/util/base";
import {TopicTitleSettingStatus} from "~/message/constants/Statuses";
import {LeaveTopicStatus} from "~/message/constants/Statuses";
import queryString from "query-string";

export default class Detail extends Base {
  constructor(props) {
    super(props);
    this.fetchLatestMessages = this.fetchLatestMessages.bind(this);
    this.handleTranslationToggle = this.handleTranslationToggle.bind(this);
    this.state = {
      back_url: '/topics',
      message_translation_active: false
    };
  }

  componentWillMount() {
    const mobile_app_footer_el = document.getElementById('MobileAppFooter');
    mobile_app_footer_el.classList.add('hidden');
    mobile_app_footer_el.dataset.isAlwaysHidden = true;

    // Set resource ID included in url.
    const topic_id = this.props.params.topic_id;
    this.props.setResourceId(topic_id);
    const {state} = this.props.location
    if (state && state.back_url) {
      this.setState({'back_url': state.back_url});
    }
    this.props.setUaInfo();
    this.props.initLayout();
    const query_params = queryString.parse(location.search);
    this.props.fetchInitialData(this.props.params.topic_id, query_params);

    // Decrease message badge count on mobile app footer as realtime
    $(document).ready(function () {
      var topic_idx = cake.unread_msg_topic_ids.indexOf(topic_id.toString());
      if (topic_idx === -1) {
        return;
      }
      cake.unread_msg_topic_ids.splice(topic_idx, 1);
      if (cake.is_mb_app) {
        setNotifyCntToMessageForMobileApp(-1, true);
      } else {
        setNotifyCntToMessageAndTitle(getMessageNotifyCnt() - 1);
      }
    });
  }

  componentDidMount() {
    super.componentDidMount.apply(this);
    // enable `routerWillLeave` method
    this.props.router.setRouteLeaveHook(this.props.route, this.routerWillLeave.bind(this));
    disableAsyncEvents()

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

      window.removeEventListener('MobileKeyboardStatusChanged', evtMobileKeyboardStatusChanged);
      window.addEventListener('MobileKeyboardStatusChanged', evtMobileKeyboardStatusChangedForTopicDetail);
    });
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.detail.input_data.body == "" && nextProps.file_upload.uploaded_file_ids.length == 0 && nextProps.detail.topic_title_setting_status == TopicTitleSettingStatus.NONE) {
      this.setState({enabled_leave_page_alert: false})
    } else {
      this.setState({enabled_leave_page_alert: true})
    }

    if (nextProps.detail.redirect) {
      browserHistory.push("/topics");
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this);

    this.props.resetStates();
    // Unsubscribe
    let {channel} = this.props.detail.pusher_info;
    channel.unbind('new_message', self.fetchLatestMessages);
    const mobile_app_footer_el = document.getElementById('MobileAppFooter');
    mobile_app_footer_el.classList.remove('hidden');
    mobile_app_footer_el.dataset.isAlwaysHidden = false;
    window.removeEventListener('MobileKeyboardStatusChanged', evtMobileKeyboardStatusChangedForTopicDetail);
    window.addEventListener('MobileKeyboardStatusChanged', evtMobileKeyboardStatusChanged);
  }

  // for SPA page route
  routerWillLeave(nextLocation) {
    if (this.props.detail.leave_topic_status != LeaveTopicStatus.SAVING  && this.state.enabled_leave_page_alert) {
      return this.state.leave_page_alert_msg
    }
  }

  fetchLatestMessages() {
    const messages = this.props.detail.messages.data;
    const displayed_latest_message_id = messages[messages.length - 1].id;
    const latest_message_id = this.props.detail.topic.latest_message_id;
    // if haven't displayed to latest, it is not updated as realtime
    if (displayed_latest_message_id < latest_message_id) {
      return;
    }
    this.props.fetchLatestMessages(displayed_latest_message_id);
  }

  handleTranslationToggle() {
    if (this.state.message_translation_active) {
      this.setState({message_translation_active: false});
    } else {
      this.setState({message_translation_active: true});
    }
  }

  render() {
    const {detail, file_upload} = this.props;
    return (
      <div className={`topicDetail ${isMobileApp() ? "mod-sp" : "panel panel-default"}`}>
        <Header
          topic={detail.topic}
          topic_title_setting_status={detail.topic_title_setting_status}
          save_topic_title_err_msg={detail.save_topic_title_err_msg}
          is_mobile_app={detail.is_mobile_app}
          mobile_app_layout={detail.mobile_app_layout}
          leave_topic_status={detail.leave_topic_status}
          leave_topic_err_msg={detail.leave_topic_err_msg}
          back_url={this.state.back_url}
          message_translation_enabled={detail.translation_enabled}
          message_translation_active={this.state.message_translation_active}
          onTranslationToggle={this.handleTranslationToggle}
        />
        <Body
          topic={detail.topic}
          search_message_id={detail.search_message_id}
          messages={detail.messages.data}
          paging={detail.messages.paging}
          is_fetched_initial={detail.is_fetched_initial}
          fetch_more_messages_status={detail.fetch_more_messages_status}
          fetch_latest_messages_status={detail.fetch_latest_messages_status}
          jump_to_latest_status={detail.jump_to_latest_status}
          last_position_message_id={detail.last_position_message_id}
          save_message_status={detail.save_message_status}
          topic_title_setting_status={detail.topic_title_setting_status}
          is_mobile_app={detail.is_mobile_app}
          mobile_app_layout={detail.mobile_app_layout}
          fetching_read_count={detail.fetching_read_count}
          is_fetched_search={detail.is_fetched_search}
          is_old_direction={detail.is_old_direction}
          message_translation_active={this.state.message_translation_active}
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
