import * as types from '../constants/ActionTypes'

const initialState = {
  checking_term: false,
  is_exception: false,
  exception_message: '',
  is_timezone_edit_mode: false,
  start_month_list: []
}

export default function term(state = initialState, action) {
  switch (action.type) {
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
    default:
      return state;
  }
}
