import { SELECT_CIRCLE_FOR_POST } from '../constants/ActionTypes'

const initialState = {
  selected_circle_id_for_post: '',
}

export default function profile(state = initialState, action) {
  switch (action.type) {
    case SELECT_CIRCLE_FOR_POST:
      return Object.assign({}, state, {
        selected_circle_id_for_post: action.selected_circle_id
      })
    default:
      return state;
  }
}
