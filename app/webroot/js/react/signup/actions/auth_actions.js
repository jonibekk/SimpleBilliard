import * as types from '../constants/ActionTypes'
import { post } from './common_actions'

export function inputCode(index, code) {
  return (dispatch, getState) => {
    let value = {}
    const state = getState()
    const key_name = `code${index}`

    if(state.auth.auth_code_is_invalid || state.auth.auth_code_is_locked || state.auth.auth_code_is_expired) {
      dispatch({ type: types.AUTH_CODE_IS_VALID })
      dispatch({ type: types.AUTH_CODE_IS_UNLOCKED })
      dispatch({ type: types.AUTH_CODE_IS_ENABLED })
    }
    dispatch({ type: types.INPUT_CODE, inputed_code: Object.assign({}, state.auth.code_list, {[key_name]: code}) })

    if(state.auth.code_list['code1'] !== '' && state.auth.code_list['code2'] !== '' && state.auth.code_list['code3'] !== '' && state.auth.code_list['code4'] !== '' && state.auth.code_list['code5'] != '' && state.auth.code_list['code6'] !== '') {
      dispatch(postVerifyCode(code))
    }
  }
}

export function postVerifyCode(code) {
  return dispatch => {
    dispatch({ type: types.AUTH_CODE_IS_VALID })
    dispatch({ type: types.AUTH_CODE_IS_UNLOCKED })
    dispatch({ type: types.AUTH_CODE_IS_ENABLED })

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
      dispatch({ type: types.INIT_AUTH_CODE })
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_AUTH_CODE })
      dispatch({ type: types.AUTH_CODE_IS_INVALID, invalid_message: 'Network error' })
      dispatch({ type: types.INIT_AUTH_CODE })
    })
  }
}
