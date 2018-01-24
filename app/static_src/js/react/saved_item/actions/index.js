import * as ActionTypes from "~/saved_item/constants/ActionTypes";
import {post, del} from "~/util/api";
import axios from "axios";
import querystring from "querystring";
import {isMobileApp} from "~/util/base";

export function fetchInitialData() {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.LOADING,
    })
    return axios.get(`/api/v1/saved_items/init_list_page` + location.search)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: ActionTypes.FETCH_INITIAL_DATA,
          data,
        })
      })
      .catch((response) => {
      })
  }
}

export function updateFilter(type) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING,
    })

    const search_conditions = {type}
    const queries = querystring.stringify(search_conditions)
    history.pushState(null, "", '?' + queries);

    return axios.get(`/api/v1/saved_items?${queries}`)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.SEARCH,
          search_conditions,
          search_result
        })
      })
      .catch((response) => {
      })
  }
}

export function fetchMore(url) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING_MORE,
    })
    return axios.get(`${url}`)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.FETCH_MORE,
          search_result
        })
      })
      .catch((response) => {
      })
  }
}

export function removeItem(item) {
  return (dispatch, getState) => {
    return del(`/api/v1/posts/${item.post_id}/saved_items`)
      .then((response) => {
        dispatch({
          type: ActionTypes.REMOVE,
          item
        })
      })
      .catch((response) => {
      })
  }
}

export function setUaInfo() {
  return {
    type: ActionTypes.SET_UA_INFO,
    is_mobile_app: isMobileApp()
  }
}
