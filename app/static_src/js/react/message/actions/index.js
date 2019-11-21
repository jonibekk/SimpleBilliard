import * as types from "~/message/constants/ActionTypes";
import { get } from "~/util/api";
import {isMobileApp} from "~/util/base";
import { sentrySend } from '~/util/sentry';

export function fetchInitData() {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING
    });

    return get('/api/v1/topics')
      .then((response) => {
        const topics = response.data.data
        const next_url = response.data.paging.next
        dispatch({
          type: types.INITIALIZE,
          data: {
            topics,
            next_url
          }
        })
      })
      .catch((e) => {
        /* eslint-disable no-console */
        console.log(e);
        /* eslint-enable no-console */
        sentrySend(e);
      })
  }
}

export function fetchMore(url) {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING
    })
    return get(url)
      .then((response) => {
        const topics = response.data.data
        const next_url = response.data.paging.next
        dispatch({
          type: types.FETCH_MORE_TOPICS,
          data: {
            topics,
            next_url
          }
        })
      })
      .catch((e) => {
        /* eslint-disable no-console */
        console.log(e);
        /* eslint-enable no-console */
        sentrySend(e);
      })
  }
}

export function changeToSearchMode() {
  return (dispatch) => {
    dispatch({
      type: types.CHANGE_TO_SEARCH_MODE
    })
  }
}

export function cancelSearchMode() {
  history.pushState(null, null, "/topics");
  return (dispatch) => {
    dispatch({
      type: types.INITIALIZE_SEARCH,
      search_type: 'topics'
    })
    dispatch({
      type: types.CHANGE_TO_INDEX_MODE
    })
  }
}

export function setUaInfo() {
  return {
    type: types.SET_UA_INFO,
    is_mobile_app: isMobileApp()
  }
}

export function emptyTopicList() {
  return (dispatch) => {
    dispatch({
      type: types.EMPTY_TOPICS
    })
  }
}
export function init_completed() {
  return (dispatch) => {
    dispatch({
      type: types.INIT_COMPLETED
    })
  }
}
