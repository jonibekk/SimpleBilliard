import * as types from '../constants/ActionTypes'

const initialState = {
  goal_approval: {
    
  }
}

export default function detail(state = initialState, action) {
  switch (action.type) {
    case types.SET_GOAL_APPROVAL:
      return Object.assign({}, state, {
        goal_approval: action.goal_approval
      })
    default:
      return state;
  }
}
