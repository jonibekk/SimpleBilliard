import * as types from '~/goal_approval/constants/ActionTypes'
import axios from "axios"

export function fetchGoalMembers(is_initialize = false) {
  return (dispatch, getState) => {
    const next_getting_api = getState().list.next_getting_api
    const default_getting_api = '/api/v1/goal_approvals/list'
    const request_api = next_getting_api ? next_getting_api : default_getting_api

    dispatch(fetchingGoalMembers())
    return axios.get(request_api, {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Cache-Control': 'no-store, private, no-cache, must-revalidate'
      },
      dataType: 'json'
    })
      .then((response) => {
        dispatch(finishedFetchingGoalMembers())
        // TODO: 第一フェーズではページネーションは行わないので全件表示する
        // dispatch(setNextPagingApi(response.paging.next))
        if (is_initialize) {
          dispatch(initGoalMembers(response.data.data.goal_members))
          dispatch(setApplicationCount(response.data.data.application_count))
          dispatch(setNextPagingApi('/api/v1/goal_approvals/list'))
          /* eslint-disable no-console */
          console.log('fetch init data')
          /* eslint-enable no-console */
        } else {
          dispatch(addGoalMembers(response.data.data.goal_members))
        }

        // TODO: 第一フェーズではページネーションは行わないので全件表示する
        // if(response.data.data.goal_members.length < List.NUMBER_OF_DISPLAY_LIST_CARD) {
        //   dispatch(doneLoadingAllData())
        // }
      })
      .catch(() => {
        dispatch(finishedFetchingGoalMembers())
      })

  }
}

export function initGoalMembers(goal_members) {
  return {type: types.INIT_COLLABORATORS, goal_members}
}

export function setApplicationCount(application_count) {
  return {type: types.SET_APPLICATION_COUNT, application_count}
}

export function addGoalMembers(goal_members) {
  return {type: types.ADD_COLLABORATORS, goal_members}
}

export function fetchingGoalMembers() {
  return {type: types.FETCHING_COLLABORATORS}
}

export function finishedFetchingGoalMembers() {
  return {type: types.FINISHED_FETCHING_COLLABORATORS}
}

export function setNextPagingApi(next_getting_api) {
  return {type: types.SET_NEXT_PAGING_API, next_getting_api}
}

export function doneLoadingAllData() {
  return {type: types.DONE_LOADING_ALL_DATA}
}
