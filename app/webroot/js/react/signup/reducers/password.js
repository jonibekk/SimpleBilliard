import * as types from '../constants/ActionTypes'

const initialState = {
  exception_message: '',
  submit_button_is_enabled: false,
  checking_password: false,
  is_exception: false
}

export default function password(state = initialState, action) {
  switch (action.type) {
    case types.CAN_SUBMIT_PASSWORD:
      return Object.assign({}, state, {
        submit_button_is_enabled: true
      })
    case types.CAN_NOT_SUBMIT_PASSWORD:
      return Object.assign({}, state, {
        submit_button_is_enabled: false
      })
    case types.CHECKING_PASSWORD:
      return Object.assign({}, state, {
        checking_password: true
      })
    case types.FINISHED_CHECKING_PASSWORD:
      return Object.assign({}, state, {
        checking_password: false
      })
    case types.PASSWORD_NETWORK_ERROR:
      return Object.assign({}, state, {
        is_exception: true,
        exception_message: action.exception_message
      })
    default:
      return state;
  }
}
