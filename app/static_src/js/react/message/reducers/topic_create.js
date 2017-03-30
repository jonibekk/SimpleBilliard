import * as ActionTypes from "~/message/constants/ActionTypes";
import {TopicTitleSettingStatus} from "~/message/constants/Statuses";

const initial_state = {
  is_saving: false,
  err_msg: "",
  input_data: {
    body: "",
    to_user_ids: [],
    file_ids: []
  }
}

export default function topic_create(state = initial_state, action) {
  let input_data = state.input_data
  switch (action.type) {
    case ActionTypes.TopicCreate.SAVING:
      return Object.assign({}, state, {
        is_saving: true
      })
    case ActionTypes.TopicCreate.SAVE_SUCCESS:
      return Object.assign({}, state, {
        is_saving: false
      })
    case ActionTypes.TopicCreate.SAVE_ERROR:
      return Object.assign({}, state, {
        err_msg: action.error.message,
        is_saving: false
      })
    case ActionTypes.TopicCreate.UPDATE_INPUT_DATA:
      input_data = Object.assign(input_data, action.input_data);
      return Object.assign({}, state, {
        input_data
      })
    default:
      return state;
  }
}
