import * as ActionTypes from "~/message/constants/ActionTypes";
import {get, post, put} from "~/util/api";
import {FileUpload} from "~/common/constants/App";
import {TopicTitleSettingStatus} from "~/message/constants/Statuses";


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
          data: response.data
        })
      },
      (response) => {
        dispatch({
          type: ActionTypes.SAVE_ERROR,
          data: response.data
        })
      }
    );
  }
}
export function sendMessage() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.SAVING})
    const {detail, file_upload} = getState();

    const postData = Object.assign(detail.input_data, {
      topic_id: detail.topic_id,
      file_ids: file_upload.uploaded_file_ids
    });
    return post("/api/v1/messages", postData, null,
      (response) => {
        dispatch({
          type: ActionTypes.SAVE_SUCCESS,
          data: response.data
        })
      },
      (response) => {
        dispatch({
          type: ActionTypes.SAVE_ERROR,
          data: response.data
        })
      }
    );
  }
}

export function onChangeMessage(val) {
  return {
    type: ActionTypes.CHANGE_MESSAGE,
    body: val
  }
}
export function setResourceId(topic_id) {
  return {
    type: ActionTypes.SET_RESOURCE_ID,
    topic_id
  }
}

export function resetStates() {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.RESET_DETAIL_STATES
    })
  }
}

export function saveTopicTitle(title) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.CHANGE_TOPIC_TITLE_SETTING_STATUS,
      topic_title_setting_status: TopicTitleSettingStatus.SAVING,
    })
    const topic_id = getState().detail.topic_id;
    const putData = {title}
    return put(`/api/v1/topics/${topic_id}`, putData, null,
      (response) => {
        dispatch({
          type: ActionTypes.SAVE_TOPIC_TITLE_SUCCESS,
          topic_title_setting_status: TopicTitleSettingStatus.NONE,
          topic: response.data.data.topic,
        })
      },
      (response) => {
        // If there is validation error, set as error message
        const err = response.response.data
        let save_topic_title_err_msg = err.message
        if (err.validation_errors) {
          save_topic_title_err_msg = err.validation_errors.title
        }
        dispatch({
          type: ActionTypes.SAVE_TOPIC_TITLE_ERROR,
          topic_title_setting_status: TopicTitleSettingStatus.EDITING,
          save_topic_title_err_msg
        })
      }
    );
  }
}

export function startTopicTitleSetting() {
  return {
    type: ActionTypes.CHANGE_TOPIC_TITLE_SETTING_STATUS,
    topic_title_setting_status: TopicTitleSettingStatus.EDITING,
  }
}

export function cancelTopicTitleSetting() {
  return {
    type: ActionTypes.CHANGE_TOPIC_TITLE_SETTING_STATUS,
    topic_title_setting_status: TopicTitleSettingStatus.NONE,
  }
}
