import {  } from '../constants/ActionTypes'

const initialState = {
  inputed_code: null,
  checking_auth_code: false,
  email_is_locked: false,
  code_is_invalid: false,
  invalid_message: ''
}

export default function password(state = initialState, action) {
  switch (action.type) {
    case INPUT_CODE:
      return Object.assign({}, state, {
        inputed_code: action.inputed_code
      })
    default:
      return state;
  }
}
