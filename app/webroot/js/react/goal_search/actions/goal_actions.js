import * as ActionTypes from "~/goal_search/constants/ActionTypes";
import {post, del} from "~/util/api";
import axios from "axios";
import querystring from "querystring";

export function updateInputData(data, key) {
  return {
    type: ActionTypes.UPDATE_INPUT_DATA,
    data,
    key
  }
}
export function updateData(data, key) {
  return {
    type: ActionTypes.UPDATE_DATA,
    data,
    key
  }
}
export function update(data) {
  return {
    type: ActionTypes.UPDATE_DATA,
    data,
  }
}

export function fetchInitialData() {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.LOADING,
    })
    let query = location.search
    return axios.get(`/api/v1/goals/init_search` + query)
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

export function updateFilter(data) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING,
    })

    // 更新した検索条件によってゴール検索を実行
    let search_conditions = Object.assign(
      getState().goal_search.search_conditions,
      data
    )
    return axios.get(`/api/v1/goals/search?${querystring.stringify(search_conditions)}`)
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

export function fetchMoreGoals(url) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING_MORE,
    })
    return axios.get(`${url}`)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.FETCH_MORE_GOALS,
          search_result
        })
      })
      .catch((response) => {
      })
  }
}

export function follow(goal_id) {
  return (dispatch, getState) => {
    return post(`/api/v1/goals/${goal_id}/follow`)
      .then((response) => {
        dispatch({
          type: ActionTypes.FOLLOW,
          goal_id
        })
      })
      .catch((response) => {
      })
  }
}

export function unfollow(goal_id) {
  return (dispatch, getState) => {
    return del(`/api/v1/goals/${goal_id}/follow`)
      .then((response) => {
        dispatch({
          type: ActionTypes.UNFOLLOW,
          goal_id
        })
      })
      .catch((response) => {
      })
  }
}
