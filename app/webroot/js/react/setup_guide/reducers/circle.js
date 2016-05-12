import { SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE, CAN_SUBMIT_CIRCLE, CAN_NOT_SUBMIT_CIRCLE } from '../constants/ActionTypes'

const initialState = {
  selected_circle_id: '',
  circles: [],
  joined: false,
  can_click_submit_button: false
}

export default function circle(state = initialState, action) {
  switch (action.type) {
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
    case CAN_SUBMIT_CIRCLE:
      return Object.assign({}, state, {
        can_click_submit_button: true
      })
    case CAN_NOT_SUBMIT_CIRCLE:
      return Object.assign({}, state, {
        can_click_submit_button: false
      })
    default:
      return state;
  }
}
