import * as types from '../constants/ActionTypes'

const initialState = {
  checking_term: false,
  is_exception: false,
  exception_message: '',
  is_timezone_edit_mode: false,
  next_range_list: []
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
    case types.SET_NEXT_RANGE_LIST:
      return Object.assign({}, state, {
        next_range_list: action.next_range_list
      })
    default:
      return state;
  }
}
