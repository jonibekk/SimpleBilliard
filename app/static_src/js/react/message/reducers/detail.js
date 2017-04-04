import * as ActionTypes from "~/message/constants/ActionTypes";
import {TopicTitleSettingStatus, FetchMoreMessages} from "~/message/constants/Statuses";

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
  loading_latest: false,
  is_fetched_initial: false,
  is_saving: false,
  success_fetch_more: false,
  topic_title_setting_status: TopicTitleSettingStatus.NONE,
  save_topic_title_err_msg: "",
  err_msg: "",
  input_data: {
    body: "",
    file_ids: []
  },
  pusher_info: {
    pusher:null,
    channel:null,
    socket_id:""
  }
}

export default function detail(state = initialState, action) {
  let messages = {};
  let input_data = state.input_data
  switch (action.type) {
    case ActionTypes.RESET_DETAIL_STATES:
      return Object.assign({}, state, initialState)
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
        data: [...state.messages.data, ...action.messages.data],
        paging: action.messages.paging,
      }
      return Object.assign({}, state, {
        messages,
        fetch_more_messages_status: FetchMoreMessages.SUCCESS,
      })
    // Fetch latest messages by pusher
    case ActionTypes.LOADING_LATEST_MESSAGES:
      return Object.assign({}, state, {
        loading_latest: true
      })
    case ActionTypes.FETCH_LATEST_MESSAGES:
      messages = {
        data: action.messages,
      }
      return Object.assign({}, state, {
        messages,
        loading_latest: false
      })

    case ActionTypes.SET_TOPIC:
      return Object.assign({}, state, {
        topic: action.topic
      })
    case ActionTypes.SAVING:
      return Object.assign({}, state, {
        is_saving: true
      })
    case ActionTypes.SAVE_SUCCESS:
      messages = {
        paging: state.messages.paging,
        data: [action.message, ...state.messages.data],
      }
      let topic = Object.assign({}, state.topic)
      topic.latest_message_id = action.message.id
      return Object.assign({}, state, {
        topic,
        messages,
        is_saving: false
      })
    case ActionTypes.SAVE_ERROR:
      return Object.assign({}, state, {
        err_msg: action.error.message,
        is_saving: false
      })
    case ActionTypes.CHANGE_TOPIC_TITLE_SETTING_STATUS:
      return Object.assign({}, state, {
        save_topic_title_err_msg: "",
        topic_title_setting_status: action.topic_title_setting_status
      })
    case ActionTypes.SAVE_TOPIC_TITLE_SUCCESS:
      return Object.assign({}, state, {
        topic: action.topic,
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
    case ActionTypes.SET_BROWSER_INFO:
      return Object.assign({}, state, {
        browser_info: action.browser_info
      })
    default:
      return state;
  }
}
