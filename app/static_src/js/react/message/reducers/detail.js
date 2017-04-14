import * as ActionTypes from "~/message/constants/ActionTypes";
import {
  FetchLatestMessageStatus,
  FetchMoreMessages,
  SaveMessageStatus,
  TopicTitleSettingStatus,
  LeaveTopicStatus
} from "~/message/constants/Statuses";

const initialState = {
  topic_id: 0,
  topic: {},
  messages: {
    data: [],
    paging: {
      next: ""
    }
  },
  last_position_message_id: 0,
  loading: false,
  fetch_more_messages_status: FetchMoreMessages.NONE,
  fetch_latest_messages_status: FetchLatestMessageStatus.NONE,
  leave_topic_status: LeaveTopicStatus.NONE,
  is_fetched_initial: false,
  save_message_status: SaveMessageStatus.NONE,
  success_fetch_more: false,
  topic_title_setting_status: TopicTitleSettingStatus.NONE,
  save_topic_title_err_msg: "",
  leave_topic_err_msg: "",
  err_msg: "",
  input_data: {
    body: "",
    file_ids: []
  },
  pusher_info: {
    pusher: null,
    channel: null,
    socket_id: ""
  },
  is_mobile_app: false,
  mobile_app_layout: {
    header_top: null,
    body_top: null,
    body_bottom: null,
    footer_bottom: null,
  }
}

export default function detail(state = initialState, action) {
  let messages = {};
  let input_data = state.input_data
  switch (action.type) {
    case ActionTypes.RESET_DETAIL_STATES:
      input_data = {
        body: "",
        file_ids: []
      };
      return Object.assign({}, state, initialState, {
        input_data,
        redirect: false
      })
    case ActionTypes.SET_RESOURCE_ID:
      return Object.assign({}, state, {
        topic_id: action.topic_id
      })
    case ActionTypes.LOADING:
      return Object.assign({}, state, {
        loading: true
      })
    case ActionTypes.FETCH_INITIAL_DATA:
      return Object.assign({}, state, action.data, {
        loading: false,
        is_fetched_initial: true
      })
    // Fetch more messages
    case ActionTypes.LOADING_MORE:
      return Object.assign({}, state, {
        fetch_more_messages_status: FetchMoreMessages.LOADING,
        last_position_message_id: action.last_position_message_id
      })
    case ActionTypes.FETCH_MORE_MESSAGES:
      messages = {
        data: [...action.messages.data, ...state.messages.data],
        paging: action.messages.paging,
      }
      return Object.assign({}, state, {
        messages,
        fetch_more_messages_status: FetchMoreMessages.SUCCESS,
      })
    // Fetch latest messages by pusher
    case ActionTypes.LOADING_LATEST_MESSAGES:
      return Object.assign({}, state, {
        fetch_latest_messages_status: FetchLatestMessageStatus.LOADING
      })
    case ActionTypes.FETCH_LATEST_MESSAGES:
      messages = {
        data: action.messages
      }
      const updated_topic = Object.assign({}, state.topic, {
        latest_message_id: action.latest_message.id,
        read_count: action.latest_message.read_count
      })
      return Object.assign({}, state, {
        messages,
        fetch_latest_messages_status: FetchLatestMessageStatus.SUCCESS,
        topic: updated_topic
      })
    case ActionTypes.SET_TOPIC_ON_DETAIL:
      return Object.assign({}, state, {
        topic: action.topic
      })
    case ActionTypes.SAVING:
      return Object.assign({}, state, {
        save_message_status: SaveMessageStatus.SAVING,
        err_msg: ""
      })
    case ActionTypes.SAVE_SUCCESS:
      messages = {
        paging: state.messages.paging,
        data: [...action.messages]
      }
      let topic = Object.assign({}, state.topic)
      topic.latest_message_id = action.messages[action.messages.length - 1].id;
      topic.read_count = 0;
      return Object.assign({}, state, {
        topic,
        messages,
        input_data: {body: "", file_ids: []},
        save_message_status: SaveMessageStatus.SUCCESS
      })
    case ActionTypes.SAVE_ERROR:
      return Object.assign({}, state, {
        err_msg: action.err_msg,
        save_message_status: SaveMessageStatus.ERROR
      })

    /* Set topic title */
    case ActionTypes.CHANGE_TOPIC_TITLE_SETTING_STATUS:
      return Object.assign({}, state, {
        save_topic_title_err_msg: "",
        topic_title_setting_status: action.topic_title_setting_status
      })
    case ActionTypes.SAVE_TOPIC_TITLE_SUCCESS:
      messages = {
        paging: state.messages.paging,
        data: [...state.messages.data, action.latest_message],
      }
      return Object.assign({}, state, {
        topic: action.topic,
        messages,
        save_topic_title_err_msg: "",
        topic_title_setting_status: action.topic_title_setting_status
      })
    case ActionTypes.SAVE_TOPIC_TITLE_ERROR:
      return Object.assign({}, state, {
        save_topic_title_err_msg: action.save_topic_title_err_msg,
        topic_title_setting_status: action.topic_title_setting_status
      })

    case ActionTypes.CHANGE_MESSAGE:
      input_data.body = action.body;
      return Object.assign({}, state, {
        input_data
      })
    case ActionTypes.SET_PUSHER_INFO:
      const pusher_info = Object.assign(state.pusher_info, action.pusher_info);
      return Object.assign({}, state, {
        pusher_info
      })
    case ActionTypes.SET_UA_INFO:
      return Object.assign({}, state, {
        is_mobile_app: action.is_mobile_app
      })
    case ActionTypes.RESET_SAVE_MESSAGE_STATUS:
      return Object.assign({}, state, {
        save_message_status: SaveMessageStatus.NONE
      })
    case ActionTypes.RESET_FETCH_MORE_MESSAGES_STATUS:
      return Object.assign({}, state, {
        fetch_more_messages_status: FetchMoreMessages.NONE
      })
    case ActionTypes.RESET_FETCH_LATEST_MESSAGES_STATUS:
      return Object.assign({}, state, {
        fetch_latest_messages_status: FetchLatestMessageStatus.SUCCESS
      })
    case ActionTypes.UPDATE_READ_COUNT:
      const new_topic = Object.assign({}, state.topic, {read_count: action.read_count})
      return Object.assign({}, state, {
        topic: new_topic
      })
    case ActionTypes.RESET_TOPIC_TITLE_SETTING_STATUS:
      return Object.assign({}, state, {
        topic_title_setting_status: TopicTitleSettingStatus.NONE
      })
    case ActionTypes.INIT_LAYOUT:
      return Object.assign({}, state, {
        mobile_app_layout: action.mobile_app_layout
      })
    case ActionTypes.CHANGE_LAYOUT:
      const mobile_app_layout = Object.assign({}, state.mobile_app_layout, action.mobile_app_layout);
      return Object.assign({}, state, {
        mobile_app_layout
      })

    // Leave topic
    case ActionTypes.LeaveTopic.SAVING:
      return Object.assign({}, state, {
        leave_topic_err_msg: "",
        leave_topic_status: LeaveTopicStatus.SAVING
      })
    case ActionTypes.LeaveTopic.SAVE_SUCCESS:
      return Object.assign({}, state, {
        leave_topic_status: LeaveTopicStatus.SUCCESS,
        redirect: true,
      })
    case ActionTypes.LeaveTopic.SAVE_ERROR:
      return Object.assign({}, state, {
        leave_topic_err_msg: action.err_msg,
        leave_topic_status: LeaveTopicStatus.ERROR
      })
    case ActionTypes.LeaveTopic.RESET_STATUS:
      return Object.assign({}, state, {
        leave_topic_err_msg: "",
        leave_topic_status: LeaveTopicStatus.NONE
      })

    default:
      return state;
  }
}
