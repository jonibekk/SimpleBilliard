import * as ActionTypes from "~/message/constants/ActionTypes";
import {TopicTitleSettingStatus} from "~/message/constants/Statuses";

const initial_state = {
  is_saving: false,
  err_msg: "",
  input_data: {
    body: "",
    file_ids: []
  }
}

export default function topic_create(state = initial_state, action) {
  let messages = {};
  let input_data = state.input_data
  switch (action.type) {
    case ActionTypes.TopicCreate.SAVING:
      return Object.assign({}, state, {
        is_saving: true
      })
    case ActionTypes.TopicCreate.SAVE_SUCCESS:
      messages = {
        data: [...state.messages.data, action.data],
        paging: state.messages.paging,
      }
      return Object.assign({}, state, {
        messages,
        is_saving: false
      })
    case ActionTypes.TopicCreate.SAVE_ERROR:
      return Object.assign({}, state, {
        err_msg: action.error.message,
        is_saving: false
      })
    case ActionTypes.CHANGE_MESSAGE:
      input_data.body = action.body;
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
