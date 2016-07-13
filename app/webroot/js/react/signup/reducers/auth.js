import { INPUT_CODE, CHECKING_AUTH_CODE, FINISHED_CHECKING_AUTH_CODE, INIT_AUTH_CODE, AUTH_CODE_IS_INVALID, INIT_AUTH_CODE_INVALID, EMAIL_IS_LOCKED, EMAIL_IS_UNLOCKED } from '../constants/ActionTypes'

const initialState = {
  inputed_code: null,
  checking_auth_code: false,
  email_is_locked: false,
  code_is_invalid: false,
  invalid_message: ''
}

export default function auth(state = initialState, action) {
  switch (action.type) {
    case INPUT_CODE:
      return Object.assign({}, state, {
        inputed_code: action.inputed_code
      })
    case CHECKING_AUTH_CODE:
      return Object.assign({}, state, {
        checking_auth_code: true
      })
    case FINISHED_CHECKING_AUTH_CODE:
      return Object.assign({}, state, {
        checking_auth_code: false
      })
    case INIT_AUTH_CODE:
      return Object.assign({}, state, {
        inputed_code: null
      })
    case AUTH_CODE_IS_INVALID:
      return Object.assign({}, state, {
        code_is_invalid: true,
        invalid_message: action.invalid_message
      })
      return INVALID_AUTH_CODE
    case INIT_AUTH_CODE_INVALID:
      return Object.assign({}, state, {
        code_is_invalid: false
      })
    case EMAIL_IS_LOCKED:
      return Object.assign({}, state, {
        email_is_locked: true
      })
    case EMAIL_IS_UNLOCKED:
      return Object.assign({}, state, {
        email_is_locked: false
      })
    default:
      return state;
  }
}
