import * as types from '../constants/ActionTypes'
import { get } from './common_actions'

export function fetchGaolApprovals() {
  return dispatch => {
    dispatch(fetchingGoalApprovals())
    return get('/goals/ajax_get_goal_approvals', response => {
      dispatch(finishedFetchingGoalApprovals())
      console.log(response.data)
      dispatch(setGoalApprovals(response.data))
    }, () => {
      dispatch(finishedFetchingGoalApprovals())
    })
  }
}

export function setGoalApprovals(goal_approvals) {
  return { type: types.SET_GOAL_APPROVALS, goal_approvals }
}

export function fetchingGoalApprovals() {
  return { type: types.FETCHING_GOAL_APPROVALS }
}

export function finishedFetchingGoalApprovals() {
  return { type: types.FINISHED_FETCHING_GOAL_APPROVALS }
}
