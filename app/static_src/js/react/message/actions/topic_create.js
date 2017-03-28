import * as ActionTypes from "~/message/constants/ActionTypes";
import {get, post, put} from "~/util/api";
import {FileUpload} from "~/common/constants/App";


export function createTopic() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.TopicCreate.SAVING})
    const {detail, file_upload} = getState();
    const postData = Object.assign(detail.input_data, {
      file_ids: file_upload.uploaded_file_ids
    });
    return post("/api/v1/topics", post_data, null,
      (response) => {
        dispatch({
          type: ActionTypes.TopicCreate.SAVE_SUCCESS,
          data: response.data
        })
      },
      (response) => {
        dispatch({
          type: ActionTypes.TopicCreate.SAVE_ERROR,
          data: response.data
        })
      }
    );
  }
}