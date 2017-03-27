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
    const detail = getState().detail;
    const postData = Object.assign(detail.input_data, {
      topic_id: detail.topic_id
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
export function deleteUploadedFile(file_index) {
  return (dispatch, getState) => {
    let all_files = getState().detail.files;
    let uploaded_file_ids = getState().detail.input_data.file_ids;
    // Delete file from preview files and uploaded files
    if (all_files[file_index]) {
      const file_id = all_files[file_index].id;
      const input_file_index = uploaded_file_ids.indexOf(file_id);
      if (input_file_index >= 0) {
        uploaded_file_ids.splice(input_file_index, 1);
      }
      all_files.splice(file_index, 1);
    }

    dispatch({
      type: ActionTypes.DELETE_UPLOADED_FILE,
      files: all_files,
      file_ids: uploaded_file_ids
    });
  }
}

// TODO:Check upload limit
// TODO:Test uploading multiple files simultaneously
export function uploadFiles(files) {
  return (dispatch, getState) => {
    let all_files = getState().detail.files;

    dispatch({type: ActionTypes.UPLOAD_START});
    // Upload file one by one
    for (let i = 0; i < files.length; i++) {
      let file = files[i];
      all_files.push(file);
      let file_index = all_files.length - 1;

      dispatch(uploading(file, file_index, all_files));

      const postData = {
        file
      };
      const options = {
        // Get upload progress
        onUploadProgress: function (progress_event) {
          file.progress_rate = Math.round((progress_event.loaded * 100) / progress_event.total);
          all_files[file_index] = file
          dispatch({
            type: ActionTypes.UPLOADING,
            files: all_files,
          })
        }
      };
      // Call api
      post("/api/v1/files/upload", postData, options,
        (response) => {
          dispatch(uploadSuccess(file, file_index, all_files, response))
        },
        (response) => {
          dispatch(uploadError(file, file_index, all_files, response))
        },
      );
    }
  }
}

export function uploading(file, file_index, all_files) {
  // Add info for display uploading preview
  file.status = FileUpload.Uploading;
  file.progress_rate = 0;
  file.id = null;
  // Display thumbnail only for image
  if (file.type.match(/image/)) {
    let reader = new FileReader;
    reader.onloadend = () => {
      file.previewUrl = reader.result;
      all_files[file_index] = file
      return {
        type: ActionTypes.UPLOADING,
        files: all_files,
      }
    }
    reader.readAsDataURL(file);
    return {
      type: ActionTypes.UPLOADING,
      files: all_files,
    }

  } else {
    file.previewUrl = "";
    all_files[file_index] = file
  }
  return {
    type: ActionTypes.UPLOADING,
    files: all_files,
  }
}

export function uploadSuccess(file, file_index, all_files, response) {
  file.status = FileUpload.Success
  file.id = response.data.data.id
  all_files[file_index] = file
  return {
    type: ActionTypes.UPLOAD_SUCCESS,
    files: all_files,
    file_id: file.id
  }
}

export function uploadError(file, file_index, all_files, response) {
  file.status = FileUpload.Error
  file.err_msg = response.response.data.message
  all_files[file_index] = file
  return {
    type: ActionTypes.UPLOAD_ERROR,
    files: all_files,
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
