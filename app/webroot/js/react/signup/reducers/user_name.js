import * as types from '../constants/ActionTypes'

const initialState = {
  checking_user_name: false,
  is_exception: false,
  exception_message: ''
}

export default function user_name(state = initialState, action) {
  switch (action.type) {
    case types.CHECKING_USER_NAME:
      return Object.assign({}, state, {
        checking_user_name: true
      })
    case types.FINISHED_CHECKING_USER_NAME:
      return Object.assign({}, state, {
        checking_user_name: false
      })
    case types.USER_NETWORK_ERROR:
      return Object.assign({}, state, {
        is_exception: true,
        exception_message: action.exception_message
      })
    default:
      return state;
  }
}
