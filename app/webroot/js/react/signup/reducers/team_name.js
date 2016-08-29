import * as types from '../constants/ActionTypes'

const initialState = {
  inputed_team_name: '',
  submit_button_is_enabled: false,
  checking_team_name: false,
  team_name_is_invalid: false,
  is_exception: false,
  exception_message: '',
  invalid_messages: {}
}

export default function team_name(state = initialState, action) {
  switch (action.type) {
    case types.INPUT_TEAM_NAME:
      return Object.assign({}, state, {
        inputed_team_name: action.inputed_team_name
      })
    case types.CAN_SUBMIT_TEAM_NAME:
      return Object.assign({}, state, {
        submit_button_is_enabled: true
      })
    case types.CAN_NOT_SUBMIT_TEAM_NAME:
      return Object.assign({}, state, {
        submit_button_is_enabled: false
      })
    case types.TEAM_NAME_IS_VALID:
      return Object.assign({}, state, {
        team_name_is_invalid: false,
        invalid_messages: action.invalid_messages
      })
    case types.TEAM_NAME_IS_INVALID:
      return Object.assign({}, state, {
        team_name_is_invalid: true,
        invalid_messages: action.invalid_messages
      })
    case types.CHECKING_TEAM_NAME:
      return Object.assign({}, state, {
        checking_team_name: true
      })
    case types.FINISHED_CHECKING_TEAM_NAME:
      return Object.assign({}, state, {
        checking_team_name: false
      })
    case types.TEAM_NAME_NETWORK_ERROR:
      return Object.assign({}, state, {
        is_exception: true,
        exception_message: action.exception_message
      })
    default:
      return state;
  }
}
