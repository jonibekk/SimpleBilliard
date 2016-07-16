import axios from 'axios'
import * as types from '../constants/ActionTypes'

export function inputName(key, name) {
  return dispatch => {
    dispatch({
      type: types.INPUT_USER_NAME,
      key,
      name
    })
  }
}

export function enableSubmitButton() {
  return { type: types.CAN_SUBMIT_USER_NAME }
}

export function disableSubmitButton() {
  return { type: types.CAN_NOT_SUBMIT_USER_NAME }
}

export function postUserName(user) {
  return dispatch => {
    dispatch({
      type: types.CHECKING_USER_NAME
    })
    const data = {
      'data[_Token][key]': 'csrf_token_key', //cake.data.csrf_token.key,
      'data[User][first_name]': user.first_name,
      'data[User][last_name]': user.last_name,
      'data[Local][first_name]': user.local_first_name,
      'data[Local][last_name]': user.local_last_name
    }
    let base_url

    if (typeof cake === "undefined") {
      base_url = 'http://127.0.0.1'
    } else {
      base_url = cake.url.route_url
    }
    return axios.post(base_url + '/signup/ajax_validation_fields', data, {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      dataType: 'json'
    })
      .then(function(response) {
        const user_name_is_invlalid = response.data.error && Object.keys(response.data.validation_msg).length

        dispatch({
          type: types.FINISHED_CHECKING_USER_NAME
        })
        if (user_name_is_invlalid) {
          dispatch({
            type: types.USER_NAME_IS_INVALID,
            invalid_messages: mapValidationMsg(response.data.validation_msg)
          })
        } else {
          dispatch({
            type: types.USER_NAME_IS_VALID
          })
        }
      })
      .catch(function() {
        dispatch({
          type: types.FINISHED_CHECKING_USER_NAME
        })
        dispatch({
          type: types.USER_NETWORK_ERROR,
          exception_message: 'Network error'
        })
      })
  }
}

export function mapValidationMsg(before_mapped_messages) {
  let result = {}
  const map = {
    'data[User][first_name]': 'first_name',
    'data[User][last_name]': 'last_name',
    'data[Local][first_name]': 'local_first_name',
    'data[Local][last_name]': 'local_last_name'
  }

  Object.keys(map).forEach(key => {
    if (before_mapped_messages[key]) {
      result[map[key]] = before_mapped_messages[key]
    }
  }, map)
  return result
}
