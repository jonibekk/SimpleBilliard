import * as ActionTypes from "~/message/constants/ActionTypes";
import {get, post, put} from "~/util/api";
import {FileUpload} from "~/common/constants/App";
import * as FileUploadModule from "~/message/modules/file_upload";
import {getErrMsg} from "./common";

export function createTopic() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.TopicCreate.SAVING})
    const {topic_create, file_upload} = getState();
    const post_data = Object.assign(topic_create.input_data, {
      file_ids: file_upload.uploaded_file_ids
    });
    return post("/api/v1/topics", post_data, null,
      (response) => {
        dispatch({
          type: ActionTypes.PREPEND_TOPIC,
          topic: response.data.data.topic
        })
        dispatch({
          type: ActionTypes.TopicCreate.SAVE_SUCCESS,
          data: response.data
        })
      },
      ({response}) => {
        const err_msg = getErrMsg(response);
        dispatch({
          type: ActionTypes.TopicCreate.SAVE_ERROR,
          err_msg
        })
      }
    );
  }
}

export function updateInputData(input_data) {
  return {
    type: ActionTypes.TopicCreate.UPDATE_INPUT_DATA,
    input_data
  }
}

export function resetStates() {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.TopicCreate.RESET_STATES
    })
    dispatch({
      type: FileUploadModule.RESET_STATE
    })
  }
}
