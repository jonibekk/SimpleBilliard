import * as ActionTypes from "~/message/constants/ActionTypes";
import {
  FetchLatestMessageStatus,
  FetchMoreMessages,
  SaveMessageStatus,
  TopicTitleSettingStatus,
  LeaveTopicStatus,
  JumpToLatest
} from "~/message/constants/Statuses";

const initialState = {
  topic_id: 0,
  topic: {},
  messages: {
    data: [],
    paging: {
      old: "",
      new: ""
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
  jump_to_latest_status: JumpToLatest.NONE,
  save_topic_title_err_msg: "",
  fetching_read_count: false,
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
  },
  is_old_direction: true,
  search_message_id: null,
  is_fetched_search: false,
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
        is_fetched_initial: true,
        search_message_id: action.search_message_id
      })
    case ActionTypes.RESET_MESSAGES:
      messages = {
        data: action.messages.data,
        paging: {
          old: action.messages.paging.next,
          new: ""
        }
      };
      return Object.assign({}, state, {
        messages,
        loading: false,
        is_old_direction: true,
        jump_to_latest_status: JumpToLatest.DONE
      })
    // Fetch more messages
    case ActionTypes.LOADING_MORE:
      return Object.assign({}, state, {
        fetch_more_messages_status: FetchMoreMessages.LOADING,
        is_old_direction: action.is_old_direction,
        last_position_message_id: action.last_position_message_id,
        is_fetched_search: true
      })
    case ActionTypes.FETCH_MORE_MESSAGES:
      let data = [];
      let paging = {};
      if (action.is_old_direction) {
        data = [...action.messages.data, ...state.messages.data];
        paging = Object.assign({}, state.messages.paging, {old: action.messages.paging.next});
      } else {
        data = [...state.messages.data, ...action.messages.data];
        paging = Object.assign({}, state.messages.paging, {new: action.messages.paging.next});
      }

      messages = {
        data,
        paging,
      }
      return Object.assign({}, state, {
        messages,
        fetch_more_messages_status: FetchMoreMessages.SUCCESS,
        is_old_direction: action.is_old_direction,
        is_fetched_search: true
      })
    // Fetch latest messages by pusher
    case ActionTypes.LOADING_LATEST_MESSAGES:
      return Object.assign({}, state, {
        fetch_latest_messages_status: FetchLatestMessageStatus.LOADING
      })
    case ActionTypes.FETCH_LATEST_MESSAGES:
      messages = {
        data: action.messages,
        paging: {
          old: state.messages.paging.old,
          new: ""
        }
      };
      const updated_topic = Object.assign({}, state.topic, {
        latest_message_id: action.latest_message_id,
        read_count: action.latest_message_read_count
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
    case ActionTypes.FETCH_READ_COUNT:
      return Object.assign({}, state, {
        fetching_read_count: true
      })
    case ActionTypes.UPDATE_READ_COUNT:
      const new_topic = Object.assign({}, state.topic, {read_count: action.read_count})
      return Object.assign({}, state, {
        topic: new_topic,
        fetching_read_count: false
      })
    case ActionTypes.RESET_TOPIC_TITLE_SETTING_STATUS:
      return Object.assign({}, state, {
        topic_title_setting_status: TopicTitleSettingStatus.NONE
      })
    case ActionTypes.SET_JUMP_TO_LATEST_STATUS:
      return Object.assign({}, state, {
        jump_to_latest_status: action.jump_to_latest_status
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
