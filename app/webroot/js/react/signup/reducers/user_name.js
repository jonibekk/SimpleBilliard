import * as types from '../constants/ActionTypes'

const initialState = {
  validate: {
    first_name: false,
    last_name: false,
    birth_year: false,
    birth_month: false,
    birth_day: false,
    privacy: false
  },
  invalid_messages: {
    first_name: '',
    last_name: '',
    birth_day: '',
    privacy: ''
  },
  checking_user_name: false,
  submit_button_is_enabled: false,
  user_name_is_invalid: true,
  is_exception: false,
  exception_message: ''
}

export default function user_name(state = initialState, action) {
  switch (action.type) {
    case types.INPUT_USER_NAME:
      return Object.assign({}, state, {
        inputed: action.inputed
      })
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
        validate: Object.assign({}, state.validate, action.validate),
        invalid_messages: Object.assign({}, state.invalid_messages, action.invalid_messages)
      })
    case types.USER_NAME_IS_VALID:
      return Object.assign({}, state, {
        user_name_is_invalid: false
      })
    case types.CAN_SUBMIT_USER_NAME:
      return Object.assign({}, state, {
        submit_button_is_enabled: true
      })
    case types.CAN_NOT_SUBMIT_USER_NAME:
      return Object.assign({}, state, {
        submit_button_is_enabled: false
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
