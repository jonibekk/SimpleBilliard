import * as types from '../constants/ActionTypes'
import { post } from "../../util/api"
import axios from "axios"

export function fetchCollaborator(collaborator_id) {
  return dispatch => {
    return axios.get(`/api/v1/goal_approvals/detail?collaborator_id=${collaborator_id}`)
      .then((response) => {
        /* eslint-disable no-console */
        console.log('fetch success')
        /* eslint-enable no-console */
        dispatch(setCollaborator(response.data.data))
      })
      .catch(() => {
        /* eslint-disable no-console */
        console.log('fetch failed')
        /* eslint-enable no-console */
        dispatch(toListPage())
      })
  }
}

export function postSetAsTarget(post_data) {
  return (dispatch) => {
    dispatch(postingSetAsTarget())

    return post(`/api/v1/goal_approvals/set_as_target`, post_data, null,
      () => {
        /* eslint-disable no-console */
        console.log('validate success')
        /* eslint-enable no-console */
        dispatch(finishedPostingSetAsTarget())
        dispatch(toListPage())
      },
      (response) => {
        dispatch(finishedPostingSetAsTarget())
        // バリデーションエラーならエラー文言表示、他の原因によるものならリストページにリダイレクト
        if(response.data.validation_errors) {
          /* eslint-disable no-console */
          console.log("validate failed");
          /* eslint-enable no-console */
          dispatch(invalid(response.data.data))
        } else {
          dispatch(toListPage())
        }
      }
    );
  }
}

export function postRemoveFromTarget(post_data) {
  return (dispatch) => {
    dispatch(postingRemovefromTarget())

    return post(`/api/v1/goal_approvals/remove_from_target`, post_data, null,
      () => {
        /* eslint-disable no-console */
        console.log('validate success')
        /* eslint-enable no-console */
        dispatch(finishedPostingRemoveFromTarget())
        dispatch(toListPage())
      },
      (response) => {
        dispatch(finishedPostingSetAsTarget())
        // バリデーションエラーならエラー文言表示、他の原因によるものならリストページにリダイレクト
        if(response.data.validation_errors) {
          /* eslint-disable no-console */
          console.log("validate failed");
          /* eslint-enable no-console */
          dispatch(invalid(response.data.data))
        } else {
          dispatch(toListPage())
        }
      }
    );
  }
}

export function setCollaborator(collaborator) {
  return { type: types.SET_COLLABORATOR, collaborator }
}

export function postingSetAsTarget() {
  return { type: types.POSTING_SET_AS_TARGET }
}

export function finishedPostingSetAsTarget() {
  return { type: types.FINISHED_POSTING_SET_AS_TARGET }
}

export function toListPage() {
  return { type: types.TO_LIST_PAGE }
}

export function postingRemovefromTarget() {
  return { type: types.POSTING_REMOVE_FROM_TARGET }
}

export function finishedPostingRemoveFromTarget() {
  return { type: types.FINISHED_POSTING_REMOVE_FROM_TARGET }
}

export function invalid(error) {
  return { type: types.INVALID, error }
}

export function initDetailPage() {
  return { type: types.INIT_DETAIL_PAGE }
}
