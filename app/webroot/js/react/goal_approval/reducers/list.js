import * as types from '../constants/ActionTypes'

const initialState = {
  goal_approvals: [],
  incomplete_count: null,
  fetching_approvals: false,
  next_getting_api: null,
  done_loading_all_data: false
}

export default function list(state = initialState, action) {
  switch (action.type) {
    case types.INIT_GOAL_APPROVALS:
      return Object.assign({}, state, {
        goal_approvals: action.goal_approvals
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
    case types.SET_NEXT_PAGING_API:
      return Object.assign({}, state, {
        next_getting_api: action.next_getting_api
      })
    case types.DONE_LOADING_ALL_DATA:
      return Object.assign({}, state, {
        done_loading_all_data: true
      })
    default:
      return state;
  }
}
