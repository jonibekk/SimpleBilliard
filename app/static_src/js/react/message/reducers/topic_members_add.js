import * as ActionTypes from "~/message/constants/ActionTypes";

const initial_state = {
  topic_id: 0,
  is_saving: false,
  err_msg: "",
  user_ids:[],
}

export default function topic_members_add(state = initial_state, action) {
  switch (action.type) {
    case ActionTypes.TopicAddMembers.SET_RESOURCE_ID:
      return Object.assign({}, state, {
        topic_id: action.topic_id
      });
    case ActionTypes.TopicAddMembers.SAVING:
      return Object.assign({}, state, {
        is_saving: true
      });
    case ActionTypes.TopicAddMembers.SAVE_SUCCESS:
      return Object.assign({}, state, {
        is_saving: false,
        redirect: true
      });
    case ActionTypes.TopicAddMembers.SAVE_ERROR:
      return Object.assign({}, state, {
        err_msg: action.err_msg,
        is_saving: false
      });
    case ActionTypes.TopicAddMembers.SELECT_USERS:
      return Object.assign({}, state, {
        user_ids: action.user_ids
      });
    case ActionTypes.TopicAddMembers.RESET_STATES:
      return Object.assign({}, initial_state);
    default:
      return state;
  }
}
