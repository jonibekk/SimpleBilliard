import * as types from "~/message/constants/ActionTypes";
import { get } from "~/util/api";
import {isMobileApp} from "~/util/base";
import * as common from "./common";

export function fetchInitData() {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING
    })
    return get('/api/v1/topics/search')
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
      .catch((response) => {
        /* eslint-disable no-console */
        console.log(response)
        /* eslint-enable no-console */
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
          type: types.FETCH_MORE_MESSAGES,
          data: {
            topics,
            next_url
          }
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
