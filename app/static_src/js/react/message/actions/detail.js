import * as ActionTypes from "~/message/constants/ActionTypes";
import * as FileUploadModule from "~/message/modules/file_upload";
import {del, get, post, put} from "~/util/api";
import {FileUpload} from "~/common/constants/App";
import {TopicTitleSettingStatus} from "~/message/constants/Statuses";
import {PositionIOSApp, PositionMobileApp} from "~/message/constants/Styles";
import {isMobileApp} from "~/util/base";
import * as common from "./common";
import {browserHistory} from "react-router";
import Noty from "noty";

export function fetchInitialData(topic_id, query_params) {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.LOADING,
    })
    const query_string = query_params.message_id ? `?message_id=${query_params.message_id}` : "";
    return get(`/api/v1/topics/${topic_id}${query_string}`)
      .then((response) => {
        const data = response.data.data
        dispatch({
          type: ActionTypes.FETCH_INITIAL_DATA,
          data,
          search_message_id: query_params.message_id ? parseInt(query_params.message_id) : null
        })
        // TODO: have to resolve to depend on gl_basic.js
        // this method defined in gl_basic.js
        // update header unread badge count
        if (!query_params.message_id) {
          updateMessageNotifyCnt()
        }
      })
      .catch(({response}) => {
        browserHistory.push('/topics');
        new Noty({
          type: 'error',
          text: response.data.message,
        }).show();

      })
  }
}
export function resetMessages() {
  return (dispatch, getState) => {
    const topic_id = getState().detail.topic_id;

    dispatch({
      type: ActionTypes.LOADING,
    })
    return get(`/api/v1/topics/${topic_id}/messages`)
      .then((response) => {
        dispatch({
          type: ActionTypes.RESET_MESSAGES,
          messages: response.data,
        })
      })
      .catch((response) => {
      })
  }
}
export function fetchMoreMessages(url, is_old_direction) {
  return (dispatch, getState) => {
    const messages = getState().detail.messages.data;
    const last_position_message_id = messages[0].id
    dispatch({
      type: ActionTypes.LOADING_MORE,
      last_position_message_id,
      is_old_direction
    })
    return get(url)
      .then((response) => {
        const messages = response.data
        dispatch({
          type: ActionTypes.FETCH_MORE_MESSAGES,
          messages,
          is_old_direction
        })
      })
      .catch((response) => {
        //TODO: implement
      })
  }
}

export function fetchLatestMessages(cursor) {
  return (dispatch, getState) => {
    const topic_id = getState().detail.topic_id
    dispatch({
      type: ActionTypes.LOADING_LATEST_MESSAGES,
    })
    return get(`/api/v1/topics/${topic_id}/messages?direction=new&cursor=${cursor}`)
      .then((response) => {
        const latest_messages = response.data.data;
        const messages = uniqueMessages(
          getState().detail.messages.data,
          latest_messages
        );
        const latest_message_read_count = response.data.latest_message_read_count
        dispatch({
          type: ActionTypes.FETCH_LATEST_MESSAGES,
          messages,
          latest_message_read_count,
          latest_message_id: latest_messages[latest_messages.length - 1].id
        })
      })
      .catch((response) => {
        //TODO: implement
      })
  }
}

/**
 * Avoid to display duplicate message
 * @param originMessages
 * @param addMessages
 * @returns messages
 */
function uniqueMessages(originMessages, addMessages) {
  if (addMessages.length == 0) {
    return originMessages;
  }
  if (originMessages.length == 0) {
    return addMessages;
  }
  let messages = [...originMessages, ...addMessages];
  let uniqueIds = [];
  // Remove duplicate topic_id element
  messages = messages.filter(function (msg, i, self) {
    if (uniqueIds.indexOf(msg.id) >= 0) {
      return false;
    }
    uniqueIds.push(msg.id);
    return true;
  });
  return messages;
}

export function sendLike() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.SAVING})
    const detail = getState().detail;
    const last_index = detail.messages.data.length - 1;
    const last_message_id = detail.messages.data[last_index].id;
    const postData = {
      topic_id: detail.topic_id,
      socket_id: detail.pusher_info.socket_id,
      last_message_id
    };
    return post("/api/v1/messages/like", postData, null,
      (response) => {
        const messages = uniqueMessages(
          detail.messages.data,
          response.data.data.latest_messages
        );

        dispatch({
          type: ActionTypes.SAVE_SUCCESS,
          messages
        })
      },
      ({response}) => {
        const err_msg = common.getErrMsg(response);
        dispatch({
          type: ActionTypes.SAVE_ERROR,
          err_msg
        })
      }
    );
  }
}
export function sendMessage() {
  return (dispatch, getState) => {
    dispatch({type: ActionTypes.SAVING})
    const {detail, file_upload} = getState();
    const last_index = detail.messages.data.length - 1;
    const last_message_id = detail.messages.data[last_index].id;
    const postData = Object.assign(detail.input_data, {
      topic_id: detail.topic_id,
      file_ids: file_upload.uploaded_file_ids,
      socket_id: detail.pusher_info.socket_id,
      last_message_id
    });
    return post("/api/v1/messages", postData, null,
      (response) => {
        const messages = uniqueMessages(
          detail.messages.data,
          response.data.data.latest_messages
        );

        dispatch({
          type: ActionTypes.SAVE_SUCCESS,
          messages
        })
        dispatch({
          type: FileUploadModule.RESET_STATE
        })
        var ta = document.getElementsByClassName('topicDetail-footer-inputBody')[0];
        // Reset the height
        if (!isMobileApp()) {
          autosize.update(ta);
        }else{
          ta.style.height = ""; 
        }
      },
      ({response}) => {
        const err_msg = common.getErrMsg(response);
        dispatch({
          type: ActionTypes.SAVE_ERROR,
          err_msg
        })
      }
    );
  }
}

export function inputMessage(val) {
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
export function focusInputBody(focused) {
  return {
    type: ActionTypes.FOCUS_INPUT_BODY,
    focused
  }
}

export function resetStates() {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.RESET_DETAIL_STATES
    })
    dispatch({
      type: FileUploadModule.RESET_STATE
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
    const putData = {
      title,
      socket_id: getState().detail.pusher_info.socket_id
    };
    return put(`/api/v1/topics/${topic_id}`, putData, null,
      (response) => {
        dispatch({
          type: ActionTypes.SAVE_TOPIC_TITLE_SUCCESS,
          topic_title_setting_status: TopicTitleSettingStatus.SUCCESS,
          topic: response.data.data.topic,
          latest_message: response.data.data.latest_message,
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

export function leaveTopic() {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LeaveTopic.SAVING,
    })
    const topic_id = getState().detail.topic_id;
    return del(`/api/v1/topics/${topic_id}/leave_me`, null, null,
      (response) => {
        dispatch({
          type: ActionTypes.LeaveTopic.SAVE_SUCCESS,
        })
      },
      ({response}) => {
        const err_msg = common.getErrMsg(response);
        dispatch({
          type: ActionTypes.LeaveTopic.SAVE_ERROR,
          err_msg
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

export function setPusherInfo(pusher_info) {
  return {
    type: ActionTypes.SET_PUSHER_INFO,
    pusher_info
  }
}

export function setUaInfo() {
  return {
    type: ActionTypes.SET_UA_INFO,
    is_mobile_app: isMobileApp()
  }
}
export function resetSaveMessageStatus() {
  return {
    type: ActionTypes.RESET_SAVE_MESSAGE_STATUS,
  }
}
export function resetFetchMoreMessagesStatus() {
  return {
    type: ActionTypes.RESET_FETCH_MORE_MESSAGES_STATUS,
  }
}
export function resetFetchLatestMessagesStatus() {
  return {
    type: ActionTypes.RESET_FETCH_LATEST_MESSAGES_STATUS,
  }
}

export function resetLeaveTopicStatus() {
  return {
    type: ActionTypes.LeaveTopic.RESET_STATUS,
  }
}

export function fetchReadCount(topic_id) {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.FETCH_READ_COUNT
    })
    return get(`/api/v1/topics/${topic_id}/read_members`)
      .then((response) => {
        const read_count = response.data.data.member_count
        dispatch({
          type: ActionTypes.UPDATE_READ_COUNT,
          read_count
        })
      })
      .catch((response) => {
      })
  }
}

export function resetTopicTitleSettingStatus() {
  return {
    type: ActionTypes.RESET_TOPIC_TITLE_SETTING_STATUS,
  }
}

export function initLayout() {
  const mobile_app_layout = common.getLayout();
  return {
    type: ActionTypes.INIT_LAYOUT,
    mobile_app_layout
  }
}

export function changeLayout(mobile_app_layout) {
  return {
    type: ActionTypes.CHANGE_LAYOUT,
    mobile_app_layout
  }
}

export function setJumpToLatestStatus(jump_to_latest_status) {
  return {
    type: ActionTypes.SET_JUMP_TO_LATEST_STATUS,
    jump_to_latest_status
  }
}
