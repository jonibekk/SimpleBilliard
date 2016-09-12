import * as types from '../constants/ActionTypes'

const initialState = {
  goals: [
    {
    }
  ],
  incomplete_count: null
}

export default function goal(state = initialState, action) {
  switch (action.type) {
    case types.LOAD_GOAL:
      return Object.assign({}, state, {
        goals: [...state.goals, action.goal]
      })
    case types.CALC_INCOMPLETE:
      return Object.assign({}, state, {
        incomplete_count: action.incomplete_count
      })
    default:
      return state;
  }
}
