import * as types from '../constants/ActionTypes'
import { get } from './common_actions'

export function fetchGaolApproval(goal_id) {
  return dispatch => {
    return get(`/goals/ajax_get_goal_approval/${goal_id}`, response => {
      dispatch(setGoalApproval(response.data))
    }, () => {
    })
  }
}

export function postComment() {
  
}

export function setGoalApproval(goal_approval) {
  return { type: types.SET_GOAL_APPROVAL, goal_approval }
}
