import { CREATE_CIRCLE, SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE } from '../constants/ActionTypes'

const initialState = {
  form_input: {
    circle_name: '',
    members: '',
    public_flg: '',
    circle_description: '',
    circle_image: ''
  },
  selected_circle_id: '',
  circles: [],
  joined: false
}

export default function circles(state = initialState, action) {
  switch (action.type) {
    case CREATE_CIRCLE:
      return Object.assign({}, state, {
        form_input: action.form_input
      })
    case SELECT_CIRCLE:
      return Object.assign({}, state, {
        selected_circle_id: action.selected_circle_id
      })
    case FETCH_CIRCLES:
      return Object.assign({}, state, {
        circles: action.circles
      })
    case JOIN_CIRCLE:
      return Object.assign({}, state, {
        joined: true
      })
    default:
      return state;
  }
}
