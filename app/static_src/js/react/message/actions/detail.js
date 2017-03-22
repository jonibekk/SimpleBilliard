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

export function sendLike() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.SAVING})
    const postData = {topic_id: getState().detail.topic_id};
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
export function sendMessage() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.SAVING})
    const detail = getState().detail;
    const postData = Object.assign(detail.input_data, {
      topic_id: detail.topic_id
    });
    return post("/api/v1/messages", postData, null,
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
export function uploadFiles(files) {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.UPLOADING})
    const postData = {
      "file": files[0]
    };
    return post("/api/v1/files/upload", postData, null,
      (response) => {
        dispatch({
          type: ActionTypes.UPLOAD_SUCCESS,
          // file: files[0],
          file_id : response.data.data.id
        })
      },
      (response) => {
        dispatch({
          type: ActionTypes.UPLOAD_ERROR,
          data:response.data
        })
      }
    );
  }
}
export function onChangeMessage(val) {
  return {
    type: ActionTypes.CHANGE_MESSAGE,
    message: val
  }
}
export function setResourceId(topic_id) {
  return {
    type: ActionTypes.SET_RESOURCE_ID,
    topic_id
  }
}
