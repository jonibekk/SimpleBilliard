import * as types from '../constants/ActionTypes'

const initialState = {
  invalid: {
    first_name: null,
    last_name: null,
    birth_year: null,
    birth_month: null,
    birth_day: null,
    privacy_policy: null
  },
  invalid_messages: {},
  checking_user_name: false,
  submit_button_is_enabled: false,
  is_exception: false,
  exception_message: ''
}

export default function user_name(state = initialState, action) {
  switch (action.type) {
    case types.CHECKING_USER_NAME:
      return Object.assign({}, state, {
        checking_user_name: true
      })
    case types.FINISHED_CHECKING_USER_NAME:
      return Object.assign({}, state, {
        checking_user_name: false
      })
    case types.USER_NAME_IS_INVALID:
      return Object.assign({}, state, {
        invalid: Object.assign({}, state.invalid, action.invalid),
        invalid_messages: Object.assign({}, state.invalid_messages, action.invalid_messages)
      })
    case types.USER_NAME_IS_VALID:
      return Object.assign({}, state, {
        invalid: Object.assign({}, state.invalid, action.invalid),
        invalid_messages: Object.assign({}, state.invalid_messages, action.invalid_messages)
      })
    case types.CAN_SUBMIT_USER_NAME:
      return Object.assign({}, state, {
        submit_button_is_enabled: true
      })
    case types.CAN_NOT_SUBMIT_USER_NAME:
      return Object.assign({}, state, {
        submit_button_is_enabled: false
      })
    case types.USER_NETWORK_ERROR:
      return Object.assign({}, state, {
        is_exception: true,
        exception_message: action.exception_message
      })
    default:
      return state;

  }

}
