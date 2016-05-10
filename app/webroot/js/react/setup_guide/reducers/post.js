import { SELECT_CIRCLE_FOR_POST, FETCH_CIRCLES_FOR_POST } from '../constants/ActionTypes'

const initialState = {
  selected_circle_id_for_post: '',
  circles: []
}

export default function profile(state = initialState, action) {
  switch (action.type) {
    case SELECT_CIRCLE_FOR_POST:
      return Object.assign({}, state, {
        selected_circle_id_for_post: action.selected_circle_id
      })
    case FETCH_CIRCLES_FOR_POST:
      return Object.assign({}, state, {
        circles: action.circles
      })
    default:
      return state;
  }
}
