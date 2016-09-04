import * as types from '../constants/ActionTypes'

const initialState = {
  is_exception: false,
  exception_message: '',
  checking_password: false
}

export default function password(state = initialState, action) {
  switch (action.type) {
    case types.CHECKING_PASSWORD:
      return Object.assign({}, state, {
        checking_password: true
      })
    case types.FINISHED_CHECKING_PASSWORD:
      return Object.assign({}, state, {
        checking_password: false
      })
    case types.PASSWORD_NETWORK_ERROR:
      return Object.assign({}, state, {
        is_exception: true,
        exception_message: action.exception_message
      })
    default:
      return state;
  }
}
