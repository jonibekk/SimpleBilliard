import * as types from '~/goal_approval/constants/ActionTypes'
import * as List from '~/goal_approval/constants/List'
import axios from "axios"

export function fetchCollaborators(is_initialize = false) {
  return (dispatch, getState) => {
    const next_getting_api = getState().list.next_getting_api
    const default_getting_api = '/api/v1/goal_approvals/list'
    const request_api = next_getting_api ? next_getting_api : default_getting_api

    dispatch(fetchingCollaborators())
    return axios.get(request_api, {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      dataType: 'json'
    })
    .then((response) => {
      dispatch(finishedFetchingCollaborators())
      // TODO: 仕様ではレスポンスデータに次のページングAPIに含まれていることになっているため、サーバサイドでAPI実装後コメントアウトを外す
      // dispatch(setNextPagingApi(response.paging.next))
      if(is_initialize) {
        dispatch(initCollaborators(response.data.data.collaborators))
        dispatch(setApplicationCount(response.data.data.application_count))
        dispatch(setNextPagingApi('/api/v1/goal_approvals/list'))
        console.log('fetch init data')
      } else {
        dispatch(addCollaborators(response.data.data.collaborators))
      }

      if(response.data.data.collaborators.length < List.NUMBER_OF_DISPLAY_LIST_CARD) {
        dispatch(doneLoadingAllData())
      }
    })
    .catch(() => {
      dispatch(finishedFetchingCollaborators())
    })

  }
}

export function initCollaborators(collaborators) {
  return { type: types.INIT_COLLABORATORS, collaborators }
}

export function setApplicationCount(application_count) {
  return { type: types.SET_APPLICATION_COUNT, application_count }
}

export function addCollaborators(collaborators) {
  return { type: types.ADD_COLLABORATORS, collaborators }
}

export function fetchingCollaborators() {
  return { type: types.FETCHING_COLLABORATORS }
}

export function finishedFetchingCollaborators() {
  return { type: types.FINISHED_FETCHING_COLLABORATORS }
}

export function setNextPagingApi(next_getting_api) {
  return { type: types.SET_NEXT_PAGING_API, next_getting_api }
}

export function doneLoadingAllData() {
  return { type: types.DONE_LOADING_ALL_DATA }
}
