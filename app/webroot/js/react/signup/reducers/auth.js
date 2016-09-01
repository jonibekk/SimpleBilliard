import * as types from '../constants/ActionTypes'

const initialState = {
  code_list: {
    code1: '',
    code2: '',
    code3: '',
    code4: '',
    code5: '',
    code6: ''
  },
  checking_auth_code: false,
  auth_code_is_invalid: false,
  auth_code_is_locked: false,
  auth_code_is_expired: false,
  invalid_message: '',
  locked_message: '',
  expired_message: ''
}

export default function auth(state = initialState, action) {
  switch (action.type) {
    case types.INPUT_CODE:
      return Object.assign({}, state, {
        code_list: Object.assign({}, state.code_list, action.code_list)
      })
    case types.CHECKING_AUTH_CODE:
      return Object.assign({}, state, {
        checking_auth_code: true
      })
    case types.FINISHED_CHECKING_AUTH_CODE:
      return Object.assign({}, state, {
        checking_auth_code: false
      })
    case types.INIT_AUTH_CODE:
      return Object.assign({}, state, {
        code_list: {
          code1: '',
          code2: '',
          code3: '',
          code4: '',
          code5: '',
          code6: ''
        }
      })
    case types.INIT_ALL_STATUS:
      return Object.assign({}, state, {
        auth_code_is_invalid: false,
        auth_code_is_locked: false,
        auth_code_is_expired: false
      })
    case types.AUTH_CODE_IS_INVALID:
      return Object.assign({}, state, {
        auth_code_is_invalid: true,
        invalid_message: action.invalid_message
      })
    case types.AUTH_CODE_IS_VALID:
      return Object.assign({}, state, {
        auth_code_is_invalid: false
      })
    case types.AUTH_CODE_IS_LOCKED:
      return Object.assign({}, state, {
        auth_code_is_locked: true,
        locked_message: action.locked_message
      })
    case types.AUTH_CODE_IS_UNLOCKED:
      return Object.assign({}, state, {
        auth_code_is_locked: false
      })
    case types.AUTH_CODE_IS_EXPIRED:
      return Object.assign({}, state, {
        auth_code_is_expired: true,
        expired_message: action.expired_message
      })
    case types.AUTH_CODE_IS_ENABLED:
      return Object.assign({}, state, {
        auth_code_is_expired: false
      })
    default:
      return state;
  }
}
