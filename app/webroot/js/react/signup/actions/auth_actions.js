import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import { post } from './common_actions'

export function inputCode(index, code) {
  return (dispatch, getState) => {
    const code_key = `code${index}`
    const state = getState()
    const new_code_list = Object.assign({}, state.auth.code_list, {[code_key]: code})

    // 何か入力されると同時に画面上のエラーメッセージを非表示にする
    if(state.auth.auth_code_is_invalid || state.auth.auth_code_is_locked || state.auth.auth_code_is_expired) {
      dispatch({ type: types.INIT_ALL_STATUS })
    }

    dispatch({ type: types.INPUT_CODE, code_list: new_code_list })

    // 6文字のコードを生成
    let full_code = ''

    for(const key in new_code_list) {
      full_code += String(new_code_list[key])
    }
    if(full_code.length === 6) {
      dispatch(postVerifyCode(full_code))
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
        browserHistory.push('/signup/user')
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