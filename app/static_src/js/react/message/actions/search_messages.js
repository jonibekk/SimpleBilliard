import * as types from "~/message/constants/ActionTypes";
import { get } from "~/util/api";
import {isMobileApp} from "~/util/base";
import * as common from "./common";
import {getSearchApiUrl} from "./search";
import {browserHistory} from "react-router";
import Noty from 'noty';

export function fetchInitialData(topic_id, search_conditions) {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING
    })
    const encodedKeyword = encodeURI(search_conditions.keyword);
    return get(`/api/v1/topics/${topic_id}/init_search_messages?keyword=${encodedKeyword}`)
      .then((response) => {
        const {topic, messages} = response.data;
        const next_url = getSearchNextUrl(topic_id, messages.paging);
        dispatch({
          type: types.SearchMessages.INITIALIZE,
          topic: topic,
          next_url,
          messages: messages.data,
          search_total_count: messages.count,
          search_conditions
        })
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

export function getSearchNextUrl(topic_id, paging) {
  if (!paging) {
    return '';
  }
  return `/api/v1/topics/${topic_id}/search_messages?cursor=${paging}`;
}


export function fetchMore(url) {
  return (dispatch, getState) => {
    dispatch({
      type: types.FETCHING
    })

    return get(url)
      .then((response) => {
        const {data, paging} = response.data;
        const {topic_id} = getState().search_messages;
        const next_url = getSearchNextUrl(topic_id, paging);
        dispatch({
          type: types.SearchMessages.FETCH_MORE_MESSAGES,
          messages: data,
          next_url
        })
      })
      .catch((response) => {
        /* eslint-disable no-console */
        console.log(response)
        /* eslint-enable no-console */
      })
  }
}

export function setUaInfo() {
  return {
    type: types.SET_UA_INFO,
    is_mobile_app: isMobileApp()
  }
}

export function initLayout() {
  const mobile_app_layout = common.getLayout();
  return {
    type: types.INIT_LAYOUT,
    mobile_app_layout
  }
}

export function setResourceId(topic_id) {
  return {
    type: types.SET_RESOURCE_ID,
    topic_id
  }
}

export function reset() {
  return {
    type: types.RESET,
    topic_id
  }
}
