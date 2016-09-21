import * as types from '../constants/ActionTypes'
import { get, post } from './common_actions'

export function fetchGaolApproval(goal_id) {
  return dispatch => {
    return get(`/goals/ajax_get_goal_approval/${goal_id}`, response => {
      dispatch(setGoalApproval(response.data))
    }, () => {
    })
  }
}

export function postSetAsTarget(input_data) {
  return (dispatch) => {
    dispatch(postingSetAsTarget())
    const post_data = Object.assign({}, { comment: input_data.comment })

    return post(`/api/v1/goals/set_as_target`, post_data, null,
      () => {
        dispatch(finishedPostingSetAsTarget())
        dispatch(toListPage())
      },
      (response) => {
        dispatch(finishedPostingSetAsTarget())
        if(response.data) {
          dispatch(invalid(response.data))
        }
      }
    );
  }
}

export function postRemoveFromTarget(input_data) {
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
