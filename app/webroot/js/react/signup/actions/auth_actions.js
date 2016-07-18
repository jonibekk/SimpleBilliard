import * as types from '../constants/ActionTypes'
import { post } from './common_actions'

export function inputCode(code) {
  return dispatch => {
    if(typeof code !== "number") {
      return dispatch({ type: types.INIT_AUTH_CODE })
    }
    dispatch({ type: types.INPUT_CODE, inputed_code: code })
    if(String(code).length == 6) {
      postVerifyCode(code)
    }
  }
}

export function postVerifyCode(code) {
  return dispatch => {
    dispatch({ type: types.CHECKING_AUTH_CODE })

    return post('/signup/ajax_verify_code', {'data[code]': code}, response => {
      dispatch({ type: types.FINISHED_CHECKING_AUTH_CODE })

      if(response.data.is_locked) {
        dispatch({ type: types.AUTH_CODE_IS_LOCKED, locked_message: response.data.message })
      } else if(response.data.is_expired) {
        dispatch({ type: types.AUTH_CODE_IS_EXPIRED, expired_message: response.data.message })
      } else if(response.data.error) {
        dispatch({ type: types.AUTH_CODE_IS_INVALID, invalid_message: response.data.message })
      } else {
        dispatch({ type: types.AUTH_CODE_IS_VALID })
      }
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_AUTH_CODE })
      dispatch({ type: types.AUTH_CODE_IS_INVALID, invalid_message: 'Network error' })
    })
  }
}
