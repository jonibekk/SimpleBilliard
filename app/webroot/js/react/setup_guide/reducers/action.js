import { SELECT_ACTION_GOAL, FETCH_GOALS, CAN_SUBMIT_ACTION,  CAN_NOT_SUBMIT_ACTION } from '../constants/ActionTypes'

const initialState = {
  goals: [],
  selected_action_goal: {},
  can_click_submit_button: false
}

export default function action(state = initialState, action) {
  switch (action.type) {
    case FETCH_GOALS:
      return Object.assign({}, state, {
        goals: action.goals
      })
    case SELECT_ACTION_GOAL:
      return Object.assign({}, state, {
        selected_action_goal: action.selected_action_goal
      })
    case CAN_SUBMIT_ACTION:
      return Object.assign({}, state, {
        can_click_submit_button: true
      })
    case CAN_NOT_SUBMIT_ACTION:
      return Object.assign({}, state, {
        can_click_submit_button: false
      })
    default:
      return state;
  }
}
