import { SELECT_TERM, SELECT_START_MONTH, SELECT_TIMEZONE, CHECKING_SELECTED_TERM } from '../constants/ActionTypes'

const initialState = {
  selected_term: '',
  selected_start_month: '',
  selected_time_zone: '',
  checking_term: false
}

export default function term(state = initialState, action) {
  switch (action.type) {
    case SELECT_TERM:
      return Object.assign({}, state, {
        selected_term: action.selected_term
      })
    case SELECT_START_MONTH:
      return Object.assign({}, state, {
        selected_start_month: action.selected_start_month
      })
    case SELECT_TIMEZONE:
      return Object.assign({}, state, {
        selected_time_zone: action.selected_time_zone
      })
    case CHECKING_SELECTED_TERM:
      return Object.assign({}, state, {
        checking_term: true
      })
    default:
      return state;
  }
}
