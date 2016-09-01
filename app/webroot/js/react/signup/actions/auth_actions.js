import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import { post } from './common_actions'

export function inputCode(index, code) {
  return (dispatch, getState) => {

    // 何か入力されると同時に画面上のエラーメッセージを非表示にする
    dispatch(initAllStatus())
    dispatch(inputNewCode(index, code))

    const full_code = getFullCode(getState().auth.code_list)

    if(full_code.length === 6) {
      dispatch(postVerifyCode(full_code))
    }
  }
}

export function postVerifyCode(code) {
  return dispatch => {
    dispatch(checkingAuthCode())
    return post('/signup/ajax_verify_code', {'data[code]': code}, response => {
      dispatch(finishedCheckingAuthCode())

      if(response.data.is_locked) {
        dispatch(authCodeIsLocked(response.data.message))
      } else if(response.data.is_expired) {
        dispatch(authCodeIsExpired(response.data.message))
      } else if(response.data.error) {
        dispatch(authCodeIsInvalid(response.data.message))
      } else {
        browserHistory.push('/signup/user')
        dispatch(authCodeIsValid())
      }
      dispatch(initAllCode())
    }, () => {
      dispatch(finishedCheckingAuthCode())
      dispatch(authCodeIsInvalid('Network error'))
      dispatch(initAllCode())
    })
  }
}

function getFullCode(code_list) {
  let full_code = ''

  for(const key in code_list) {
    full_code += String(code_list[key])
  }
  return full_code
}

function initAllStatus() {
  return { type: types.INIT_ALL_STATUS }
}

function initAllCode() {
  return { type: types.INIT_AUTH_CODE }
}

function inputNewCode(index, code) {
  return { type: types.INPUT_CODE, code_list: { [`code${index}`]: code } }
}

function checkingAuthCode() {
  return { type: types.CHECKING_AUTH_CODE }
}

function finishedCheckingAuthCode() {
  return { type: types.FINISHED_CHECKING_AUTH_CODE }
}

function authCodeIsLocked(message) {
  return { type: types.AUTH_CODE_IS_LOCKED, locked_message: message }
}

function authCodeIsExpired(message) {
  return { type: types.AUTH_CODE_IS_EXPIRED, expired_message: message }
}

function authCodeIsInvalid(message) {
  return { type: types.AUTH_CODE_IS_INVALID, invalid_message: message }
}

function authCodeIsValid() {
  return { type: types.AUTH_CODE_IS_VALID }
}
