import { SELECT_ACTION_GOAL, FETCH_GOALS } from '../constants/ActionTypes'

const initialState = {
  goals: [],
  selected_action_goal: {
    id: ''
  },
}

export default function actions(state = initialState, action) {
  switch (action.type) {
    case FETCH_GOALS:
      return Object.assign({}, state, {
        goals: action.goals
      })
    case SELECT_ACTION_GOAL:
      return Object.assign({}, state, {
        selected_action_goal: {
          id: action.selected_action_goal.id
        }
      })
    default:
      return state;
  }
}
