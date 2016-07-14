import { SELECT_TERM, SELECT_START_MONTH, SELECT_TIMEZONE, CHECKING_SELECTED_TERM } from '../constants/ActionTypes'

const initialState = {
  selected_term: '',
  selected_start_month: ''
}

export default function term(state = initialState, action) {
  switch (action.type) {
    case SELECT_TERM:
      return Object.assign({}, state, {
        inputed_team_name: action.selected_term
      })
    case SELECT_START_MONTH:
      return Object.assign({}, state, {
        team_name_is_invalid: false,
        invalid_message: action.selected_start_month
      })
    case SELECT_TIMEZONE:
      return Object.assign({}, state, {
        checking_team_name: true
      })
    case CHECKING_SELECTED_TERM:
      return Object.assign({}, state, {
        checking_team_name: true
      })
    default:
      return state;
  }
}
