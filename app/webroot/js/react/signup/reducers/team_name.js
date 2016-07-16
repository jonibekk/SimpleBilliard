import {
  INPUT_TEAM_NAME,
  TEAM_NAME_IS_INVALID,
  CHECKING_INPUT_TEAM_NAME
} from '../constants/ActionTypes'

const initialState = {
  inputed_team_name: '',
  checking_team_name: false,
  team_name_is_invalid: false,
  invalid_message: ''
}

export default function team_name(state = initialState, action) {
  switch (action.type) {
    case INPUT_TEAM_NAME:
      return Object.assign({}, state, {
        inputed_team_name: action.inputed_team_name
      })
    case TEAM_NAME_IS_INVALID:
      return Object.assign({}, state, {
        team_name_is_invalid: true,
        invalid_message: action.invalid_message
      })
    case CHECKING_INPUT_TEAM_NAME:
      return Object.assign({}, state, {
        checking_team_name: true
      })
    default:
      return state;
  }
}
