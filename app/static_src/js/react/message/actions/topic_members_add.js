import * as ActionTypes from "~/message/constants/ActionTypes";
import {get, post, put} from "~/util/api";
import {FileUpload} from "~/common/constants/App";
import {getErrMsg} from "./common";

export function addMembers() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.TopicAddMembers.SAVING})
    const state = getState().topic_members_add;
    const post_data = {
      user_ids: state.user_ids
    };
    return post(`/api/v1/topics/${state.topic_id}/members`, post_data, null,
      (response) => {
        dispatch({
          type: ActionTypes.TopicAddMembers.SAVE_SUCCESS,
          data: response.data
        })
      },
      ({response}) => {
        const err_msg = getErrMsg(response);
        dispatch({
          type: ActionTypes.TopicAddMembers.SAVE_ERROR,
          err_msg
        })
      }
    );
  }
}

export function selectUsers(user_ids) {
  return {
    type: ActionTypes.TopicAddMembers.SELECT_USERS,
    user_ids
  }
}
export function setResourceId(topic_id) {
  return {
    type: ActionTypes.TopicAddMembers.SET_RESOURCE_ID,
    topic_id
  }
}

export function resetStates() {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.TopicAddMembers.RESET_STATES
    })
  }
}
