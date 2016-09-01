import * as types from '../constants/ActionTypes'

const initialState = {
  invalid: {
    term: null,
    start_month: null
  },
  invalid_messages: {},
  submit_button_is_enabled: false,
  selected_term: '',
  selected_start_month: '',
  selected_timezone: '',
  checking_term: false,
  is_exception: false,
  exception_message: '',
  is_timezone_edit_mode: false,
  start_month_list: []
}

export default function term(state = initialState, action) {
  switch (action.type) {
    case types.SELECT_TERM:
      return Object.assign({}, state, {
        selected_term: action.selected_term
      })
    case types.SELECT_START_MONTH:
      return Object.assign({}, state, {
        selected_start_month: action.selected_start_month
      })
    case types.SELECT_TIMEZONE:
      return Object.assign({}, state, {
        selected_timezone: action.selected_timezone
      })
    case types.CAN_SUBMIT_TERM:
      return Object.assign({}, state, {
        submit_button_is_enabled: true
      })
    case types.CAN_NOT_SUBMIT_TERM:
      return Object.assign({}, state, {
        submit_button_is_enabled: false
      })
    case types.CHECKING_TERM:
      return Object.assign({}, state, {
        checking_term: true
      })
    case types.FINISHED_CHECKING_TERM:
      return Object.assign({}, state, {
        checking_term: false
      })
    case types.TERM_NETWORK_ERROR:
      return Object.assign({}, state, {
        is_exception: true,
        exception_message: action.exception_message
      })
    case types.CHANGE_TO_TIMEZONE_EDIT_MODE:
      return Object.assign({}, state, {
        is_timezone_edit_mode: true
      })
    case types.CHANGE_TO_TIMEZONE_NOT_EDIT_MODE:
      return Object.assign({}, state, {
        is_timezone_edit_mode: false
      })
    case types.SET_START_MONTH_LIST:
      return Object.assign({}, state, {
        start_month_list: action.start_month_list
      })
    case types.TERM_IS_INVALID:
      return Object.assign({}, state, {
        invalid: Object.assign({}, state.invalid, action.invalid),
        invalid_messages: Object.assign({}, state.invalid_messages, action.invalid_messages)
      })
    case types.TERM_IS_VALID:
      return Object.assign({}, state, {
        invalid: Object.assign({}, state.invalid, action.invalid),
        invalid_messages: Object.assign({}, state.invalid_messages, action.invalid_messages)
      })
    default:
      return state;
  }
}
