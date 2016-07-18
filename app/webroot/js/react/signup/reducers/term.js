import * as types from '../constants/ActionTypes'

const initialState = {
  submit_button_is_enabled: false,
  selected_term: '',
  selected_start_month: '',
  selected_time_zone: '',
  checking_term: false,
  term_is_invalid: false,
  invalid_message: '',
  is_exception: false,
  exception_message: '',
  is_timezone_edit_mode: false
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
        selected_time_zone: action.selected_time_zone
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
    case types.TERM_IS_VALID:
      return Object.assign({}, state, {
        term_is_invalid: false
      })
    case types.TERM_IS_INVALID:
      return Object.assign({}, state, {
        term_is_invalid: true,
        invalid_message: action.invalid_message
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
    default:
      return state;
  }
}
