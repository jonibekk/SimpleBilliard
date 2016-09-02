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
    default:
      return state;
  }
}
