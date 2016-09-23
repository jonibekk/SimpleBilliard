import * as types from '../constants/ActionTypes'
import { post } from "../../util/api"
import axios from "axios"

export function fetchGoalApproval(goal_id) {
  return dispatch => {
    return axios.get(`/api/v1/goal_approvals/${goal_id}/detail`)
      .then((response) => {
        /* eslint-disable no-console */
        console.log('fetch success')
        /* eslint-enable no-console */
        dispatch(setGoalApproval(response.data.data))
      })
      .catch(() => {
        /* eslint-disable no-console */
        console.log('fetch failed')
        /* eslint-enable no-console */
      })
  }
}

export function postSetAsTarget(input_data) {
  return (dispatch) => {
    dispatch(postingSetAsTarget())
    const post_data = Object.assign({}, { comment: input_data.comment })

    return post(`/api/v1/goal_approvals/set_as_target`, post_data, null,
      () => {
        /* eslint-disable no-console */
        console.log('validate success')
        /* eslint-enable no-console */
        dispatch(finishedPostingSetAsTarget())
        dispatch(toListPage())
      },
      (response) => {
        /* eslint-disable no-console */
        console.log("validate failed");
        /* eslint-enable no-console */
        dispatch(finishedPostingSetAsTarget())
        dispatch(invalid(response.data))
      }
    );
  }
}

export function postRemoveFromTarget(input_data) {
  return (dispatch) => {
    dispatch(postingRemovefromTarget())

    return post(`/api/v1/goal_approvals/remove_from_target`, input_data, null,
      () => {
        /* eslint-disable no-console */
        console.log('validate success')
        /* eslint-enable no-console */
        dispatch(finishedPostingRemoveFromTarget())
        dispatch(toListPage())
      },
      (response) => {
        /* eslint-disable no-console */
        console.log("validate failed");
        /* eslint-enable no-console */
        dispatch(finishedPostingRemoveFromTarget())
        dispatch(invalid(response.data))
      }
    );
  }
}

export function setGoalApproval(goal_approval) {
  return { type: types.SET_GOAL_APPROVAL, goal_approval }
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
