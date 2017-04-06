import {get, post, put} from "~/util/api";
import {FileUpload} from "~/common/constants/App";

export const UPLOAD_START = 'file_upload/UPLOAD_START'
export const UPLOADING = 'file_upload/UPLOADING'
export const UPLOAD_SUCCESS = 'file_upload/UPLOAD_SUCCESS'
export const UPLOAD_ERROR = 'file_upload/UPLOAD_ERROR'
export const DELETE_UPLOADED_FILE = 'file_upload/DELETE_UPLOADED_FILE'
export const RESET_STATE = 'file_upload/RESET_STATE'

export function deleteUploadedFile(file_index) {
  return (dispatch, getState) => {
    let {preview_files, uploaded_file_ids} = getState().file_upload;
    // Delete file from preview files and uploaded files
    if (preview_files[file_index]) {
      const file_id = preview_files[file_index].id;
      const input_file_index = uploaded_file_ids.indexOf(file_id);
      if (input_file_index >= 0) {
        uploaded_file_ids.splice(input_file_index, 1);
      }
      preview_files.splice(file_index, 1);
    }

    dispatch({
      type: DELETE_UPLOADED_FILE,
      preview_files,
      uploaded_file_ids
    });
  }
}

// TODO:Check upload limit
// TODO:Test uploading multiple files simultaneously
export function uploadFiles(files) {
  return (dispatch, getState) => {
    let preview_files = getState().file_upload.preview_files;

    dispatch({type: UPLOAD_START});
    // Upload file one by one
    for (let i = 0; i < files.length; i++) {
      let file = files[i];
      preview_files.push(file);
      let file_index = preview_files.length - 1;

      dispatch(uploading(file, file_index, preview_files));

      const postData = {
        file
      };
      const options = {
        // Get upload progress
        onUploadProgress: function (progress_event) {
          file.progress_rate = Math.round((progress_event.loaded * 100) / progress_event.total);
          preview_files[file_index] = file
          dispatch({
            type: UPLOADING,
            preview_files: preview_files,
          })
        }
      };
      // Call api
      post("/api/v1/files/upload", postData, options,
        (response) => {
          dispatch(uploadSuccess(file, file_index, preview_files, response))
        },
        (response) => {
          dispatch(uploadError(file, file_index, preview_files, response))
        },
      );
    }
  }
}

export function uploading(file, file_index, preview_files) {
  // Add info for display uploading preview
  file.status = FileUpload.Uploading;
  file.progress_rate = 0;
  file.id = null;
  // Display thumbnail only for image
  if (file.type.match(/image/)) {
    let reader = new FileReader;
    reader.onloadend = () => {
      file.previewUrl = reader.result;
      preview_files[file_index] = file
      return {
        type: UPLOADING,
        preview_files: preview_files,
      }
    }
    reader.readAsDataURL(file);
    return {
      type: UPLOADING,
      preview_files: preview_files,
    }

  } else {
    file.previewUrl = "";
    preview_files[file_index] = file
  }
  return {
    type: UPLOADING,
    preview_files: preview_files,
  }
}

export function uploadSuccess(file, file_index, preview_files, response) {
  file.status = FileUpload.Success
  file.id = response.data.data.id
  preview_files[file_index] = file
  return {
    type: UPLOAD_SUCCESS,
    preview_files: preview_files,
    uploaded_file_id: file.id
  }
}
export function resetState() {
  return {
    type: RESET_STATE,
  }

}

export function uploadError(file, file_index, preview_files, response) {
  file.status = FileUpload.Error
  file.err_msg = response.response.data.message
  preview_files[file_index] = file
  return {
    type: UPLOAD_ERROR,
    preview_files: preview_files,
  }
}

const initial_state = {
  preview_files: [],
  uploaded_file_ids: [],
  is_uploading: false,
}

export function file_upload(state = initial_state, action) {
  switch (action.type) {
    case UPLOAD_START:
      return Object.assign({}, state, {
        is_uploading: true
      })
    case UPLOADING:
      return Object.assign({}, state, {
        preview_files: [...action.preview_files]
      })
    case UPLOAD_SUCCESS:
      const uploaded_file_ids = [...state.uploaded_file_ids, action.uploaded_file_id];
      return Object.assign({}, state, {
        uploaded_file_ids,
        preview_files: [...action.preview_files],
        is_uploading: false
      })
    case UPLOAD_ERROR:
      return Object.assign({}, state, {
        preview_files: [...action.preview_files],
        is_uploading: false
      })
    case DELETE_UPLOADED_FILE:
      return Object.assign({}, state, {
        uploaded_file_ids: [...action.uploaded_file_ids],
        preview_files: [...action.preview_files],
      })
    case RESET_STATE:
      return Object.assign({}, state, {
        uploaded_file_ids: [],
        preview_files: [],
      })
    default:
      return state;
  }
}
