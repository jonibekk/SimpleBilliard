import { SUBMIT_GOAL, SELECT_PURPOSE, SELECT_GOAL } from '../constants/ActionTypes'

const initialState = {
  selected_purpose: {
    name: ''
  },
  selected_goal: {
    name: ''
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

    default:
      return state;
  }
}
