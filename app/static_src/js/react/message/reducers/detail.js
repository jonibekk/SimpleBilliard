import * as ActionTypes from "~/message/constants/ActionTypes";
import {TopicTitleSettingStatus} from "~/message/constants/Statuses";

const initialState = {
  topic_id: 0,
  topic: {},
  messages: {
    data: [],
    paging: {
      next: ""
    }
  },
  loading: false,
  loading_more: false,
  is_fetched_initial: false,
  is_saving: false,
  topic_title_setting_status: TopicTitleSettingStatus.NONE,
  save_topic_title_err_msg: "",
  err_msg: "",
  files: [],
  input_data: {
    message: "",
    file_ids: []
  }
}

export default function detail(state = initialState, action) {
  let messages = {};
  let input_data = state.input_data
  switch (action.type) {
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
    case ActionTypes.FETCH_MORE_MESSAGES:
      messages = {
        data: [...state.messages.data, ...action.messages.data],
        paging: action.messages.paging,
      }
      return Object.assign({}, state, {
        messages,
        loading_more: false
      })
    case ActionTypes.SAVING:
      return Object.assign({}, state, {
        is_saving: true
      })
    case ActionTypes.SAVE_SUCCESS:
      messages = {
        data: [...state.messages.data, action.data],
        paging: state.messages.paging,
      }
      return Object.assign({}, state, {
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
    case ActionTypes.UPLOAD_START:
      return Object.assign({}, state, {
        is_saving: true
      })
    case ActionTypes.UPLOADING:
      return Object.assign({}, state, {
        files: [...action.files]
      })
    case ActionTypes.UPLOAD_SUCCESS:
      input_data.file_ids = [...input_data.file_ids, action.file_id];
      return Object.assign({}, state, {
        input_data,
        files: [...action.files],
        is_saving: false
      })
    case ActionTypes.UPLOAD_ERROR:
      return Object.assign({}, state, {
        files: [...action.files],
        is_saving: false
      })
    case ActionTypes.CHANGE_MESSAGE:
      input_data.message = action.message;
      return Object.assign({}, state, {
        input_data
      })
    case ActionTypes.DELETE_UPLOADED_FILE:
      input_data.file_ids = [...action.file_ids]
      return Object.assign({}, state, {
        input_data,
        files: [...action.files],
      })
    default:
      return state;
  }
}
