import * as types from '../constants/ActionTypes'
import axios from "axios"

export function fetchGoalApprovals(is_initialize = false) {
  return (dispatch, getState) => {
    const next_getting_api = getState().list.next_getting_api
    const default_getting_api = '/goals/ajax_get_init_goal_approvals'
    const request_api = next_getting_api ? next_getting_api : default_getting_api

    dispatch(fetchingGoalApprovals())
    return axios.get(request_api, {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      dataType: 'json'
    })
    .then((response) => {
      dispatch(finishedFetchingGoalApprovals())
      // TODO: 仕様ではレスポンスデータに次のページングAPIに含まれていることになっているため、サーバサイドでAPI実装後コメントアウトを外す
      // dispatch(setNextPagingApi(response.paging.next))
      dispatch(setNextPagingApi('/goals/ajax_get_next_goal_approvals'))
      if(is_initialize) {
        dispatch(initGoalApprovals(response.data))
      } else {
        dispatch(addGoalApprovals(response.data))
      }

      if(response.data.length === 0 || getState().list.goal_approvals.length > 9) {
        dispatch(doneLoadingAllData())
      }
    })
    .catch(() => {
      dispatch(finishedFetchingGoalApprovals())
    })

  }
}

export function initGoalApprovals(goal_approvals) {
  return { type: types.INIT_GOAL_APPROVALS, goal_approvals }
}

export function addGoalApprovals(goal_approvals) {
  return { type: types.ADD_GOAL_APPROVALS, goal_approvals }
}

export function fetchingGoalApprovals() {
  return { type: types.FETCHING_GOAL_APPROVALS }
}

export function finishedFetchingGoalApprovals() {
  return { type: types.FINISHED_FETCHING_GOAL_APPROVALS }
}

export function setNextPagingApi(next_getting_api) {
  return { type: types.SET_NEXT_PAGING_API, next_getting_api }
}

export function doneLoadingAllData() {
  return { type: types.DONE_LOADING_ALL_DATA }
}
