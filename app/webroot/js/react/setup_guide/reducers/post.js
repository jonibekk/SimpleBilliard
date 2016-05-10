import { SELECT_CIRCLE_FOR_POST, FETCH_CIRCLES_FOR_POST } from '../constants/ActionTypes'

const initialState = {
  circles: [],
  selected_circle: {}
}

export default function post(state = initialState, action) {
  switch (action.type) {
    case SELECT_CIRCLE_FOR_POST:
      return Object.assign({}, state, {
        selected_circle: action.selected_circle
      })
    case FETCH_CIRCLES_FOR_POST:
      return Object.assign({}, state, {
        circles: action.circles
      })
    default:
      return state;
  }
}
