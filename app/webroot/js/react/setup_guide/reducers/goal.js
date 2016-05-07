import { CREATE_GOAL, SELECT_PURPOSE, SELECT_GOAL } from '../constants/ActionTypes'

const initialState = {
  selected_purpose: {
    name: ''
  },
  selected_goal: {
    name: ''
  },
  input_goal: {

  }
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
    default:
      return state;
  }
}
