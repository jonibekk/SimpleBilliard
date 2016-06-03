import { CREATE_GOAL, SELECT_PURPOSE, SELECT_GOAL, INITIALIZE_SELECTED_GOAL } from '../constants/ActionTypes'

const initialState = {
  selected_purpose: {},
  selected_goal: {},
  input_goal: {}
}

export default function goal(state = initialState, action) {
  switch (action.type) {
    case SELECT_PURPOSE:
      return Object.assign({}, state, {
        selected_purpose: action.selected_purpose
      })
    case SELECT_GOAL:
      return Object.assign({}, state, {
        selected_goal: action.selected_goal
      })
    case CREATE_GOAL:
      return Object.assign({}, state, {
        input_goal: action.goal
      })
    case INITIALIZE_SELECTED_GOAL:
      return Object.assign({}, state, {
        selected_purpose: initialState.selected_purpose,
        selected_goal: initialState.selected_goal
      })
    default:
      return state;
  }
}
