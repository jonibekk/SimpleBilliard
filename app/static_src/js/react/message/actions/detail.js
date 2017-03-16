import * as ActionTypes from "~/message/constants/ActionTypes";
import {get, post} from "~/util/api";

export function fetchInitialData(topic_id) {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.LOADING,
    })
    return get(`/api/v1/topics/${topic_id}`)
      .then((response) => {
        const data = response.data.data
        dispatch({
          type: ActionTypes.FETCH_INITIAL_DATA,
          data,
        })
      })
      .catch((response) => {
      })
  }
}
export function fetchMoreMessages(url) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING_MORE,
    })
    return get(url)
      .then((response) => {
        const messages = response.data
        dispatch({
          type: ActionTypes.FETCH_MORE_MESSAGES,
          messages
        })
      })
      .catch((response) => {
      })
  }
}

export function sendLike(topic_id) {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.SAVING})
    const postData = {topic_id};
    return post("/api/v1/messages/like", postData, null,
      (response) => {
        dispatch({
          type: ActionTypes.SAVE_SUCCESS,
          data:response.data
        })
      },
      (response) => {
        dispatch({
          type: ActionTypes.SAVE_ERROR,
          data:response.data
        })
      }
    );
  }
}
