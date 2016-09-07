import * as types from '../constants/ActionTypes'

const initialState = {
  sample: ''
}

export default function goal(state = initialState, action) {
  switch (action.type) {
    case types.SAMPLE:
      return Object.assign({}, state, {sample: action.sample})
    default:
      return state;
  }
}
