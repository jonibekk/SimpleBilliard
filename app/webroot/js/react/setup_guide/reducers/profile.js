import { CAN_SUBMIT_PROFILE, CAN_NOT_SUBMIT_PROFILE, ADD_PROFILE } from '../constants/ActionTypes'

const initialState = {
  can_click_submit_button: false,
  form_input: {
    User: {
      photo: ''
    },
    TeamMember: {
      0: {
        comment: ''
      }
    }
  }
}

export default function profile(state = initialState, action) {
  switch (action.type) {
    case CAN_SUBMIT_PROFILE:
      return Object.assign({}, state, {
        can_click_submit_button: true
      })
    case CAN_NOT_SUBMIT_PROFILE:
      return Object.assign({}, state, {
        can_click_submit_button: false
      })
    case ADD_PROFILE:
      return Object.assign({}, state, {
        form_input: action.form_input
      })
    default:
      return state;
  }
}
