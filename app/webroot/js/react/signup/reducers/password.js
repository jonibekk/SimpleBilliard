import * as types from '../constants/ActionTypes'

const initialState = {
  inputed_password: '',
  submit_button_is_enabled: false,
  checking_password: false,
  password_is_invalid: false,
  is_exception: false,
  exception_message: '',
  invalid_messages: {
    password: ''
  }
}

export default function password(state = initialState, action) {
  switch (action.type) {
    case types.INPUT_PASSWORD:
      return Object.assign({}, state, {
        inputed_password: action.inputed_password
      })
    case types.CAN_SUBMIT_PASSWORD:
      return Object.assign({}, state, {
        submit_button_is_enabled: true
      })
    case types.CAN_NOT_SUBMIT_PASSWORD:
      return Object.assign({}, state, {
        submit_button_is_enabled: false
      })
    case types.PASSWORD_IS_VALID:
      return Object.assign({}, state, {
        password_is_invalid: false
      })
    case types.PASSWORD_IS_INVALID:
      return Object.assign({}, state, {
        password_is_invalid: true,
        invalid_messages: action.invalid_messages
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
