import * as types from '../constants/ActionTypes'
import { get } from './common_actions'

export function fetchGaolApprovals(is_initialize = false) {
  return dispatch => {
    dispatch(fetchingGoalApprovals())
    return get('/goals/ajax_get_goal_approvals', response => {
      dispatch(finishedFetchingGoalApprovals())
      if(is_initialize) {
        dispatch(initGoalApprovals(response.data))
      } else {
        dispatch(addGoalApprovals(response.data))
      }
      dispatch(setLastLoadedGoalId(goal_id = response.data.pop().id))
    }, () => {
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

export function addGoalApproval(goal_approval) {
  return { type: types.ADD_GOAL_APPROVAL, goal_approval }
}

export function fetchingGoalApprovals() {
  return { type: types.FETCHING_GOAL_APPROVALS }
}

export function finishedFetchingGoalApprovals() {
  return { type: types.FINISHED_FETCHING_GOAL_APPROVALS }
}

export function setLastLoadedGoalId(goal_id) {
  return { type: types.SET_LAST_LOADED_GOAL_ID, goal_id }
}
