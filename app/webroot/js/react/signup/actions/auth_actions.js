import axios from 'axios'
import * as types from '../constants/ActionTypes'

export function postVerifyCode(code) {
  return dispatch => {
    dispatch({ type: types.CHECKING_AUTH_CODE })
    const data = {
      'data[_Token][key]': 'csrf_token_key', //cake.data.csrf_token.key,
      'data[code]': code
    }
    var base_url
    if(typeof cake === "undefined") {
      base_url = 'http://127.0.0.1'
    } else {
      base_url = cake.url.route_url
    }
    return axios.post(base_url + '/signup/ajax_verify_code', data, {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
      dataType: 'json',
    })
    .then(function (response) {
      if(response.data.is_locked) {
        dispatch({ type: types.AUTH_CODE_IS_LOCKED, locked_message: response.data.message })
      } else if(response.data.is_expired) {
        dispatch({ type: types.AUTH_CODE_IS_EXPIRED, expired_message: response.data.message })
      } else if(response.data.error) {
        dispatch({ type: types.AUTH_CODE_IS_INVALID, invalid_message: response.data.message })
      } else {
        dispatch({ type: types.AUTH_CODE_IS_VALID })
      }
    })
    .catch(function (response) {
      dispatch({ type: types.AUTH_CODE_IS_INVALID, invalid_message: 'Network error' })
    })
  }
}
