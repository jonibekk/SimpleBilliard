import * as types from '../constants/ActionTypes'

const initialState = {
  goal_approvals: [],
  incomplete_count: null,
  fetching_approvals: false,
  last_loaded_goal_id: null
}

export default function goal_approval(state = initialState, action) {
  switch (action.type) {
    case types.INIT_GOAL_APPROVALS:
      return Object.assign({}, state, {
        goal_approvals: action.goal_approvals
      })
    case types.ADD_GOAL_APPROVAL:
      return Object.assign({}, state, {
        goal_approvals: [...state.goal_approvals, action.goal_approval]
      })
    case types.ADD_GOAL_APPROVALS:
      return Object.assign({}, state, {
        goal_approvals: [...state.goal_approvals, ...action.goal_approvals]
      })
    case types.SET_INCOMPLETE_COUNT:
      return Object.assign({}, state, {
        incomplete_count: action.incomplete_count
      })
    case types.FETCHING_GOAL_APPROVALS:
      return Object.assign({}, state, {
        fetching_goal_approvals: true
      })
    case types.FINISHED_FETCHING_GOAL_APPROVALS:
      return Object.assign({}, state, {
        fetching_goal_approvals: false
      })
    case types.SET_LAST_LOADED_GOAL_ID:
      return Object.assign({}, state, {
        last_loaded_goal_id: action.goal_id
      })
    default:
      return state;
  }
}
