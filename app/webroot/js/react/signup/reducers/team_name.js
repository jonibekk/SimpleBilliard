import * as types from '../constants/ActionTypes'

const initialState = {
  checking_team_name: false,
  is_exception: false,
  exception_message: ''
}

export default function team_name(state = initialState, action) {
  switch (action.type) {
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
