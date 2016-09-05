import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import { post, mapValidationMsg } from './common_actions'
import {
  invalid
} from './validate_actions'

export function postPassword(password) {
  return dispatch => {
    dispatch(checkingPassword())

    return post('/signup/ajax_validation_fields', {'data[User][password]': password}, response => {
      const password_is_invlalid = response.data.error && Object.keys(response.data.validation_msg).length

      dispatch(finishedCheckingPassword())
      if (password_is_invlalid) {
        const error_messages = mapValidationMsg(response.data.validation_msg)

        for (const name in error_messages) {
          dispatch(invalid(name, error_messages[name]))
        }
      } else {
        browserHistory.push('/signup/team')
      }
    }, () => {
      dispatch(finishedCheckingPassword())
      dispatch(networkError())
    })
  }
}

export function checkingPassword() {
  return { type: types.CHECKING_PASSWORD }
}

export function finishedCheckingPassword() {
  return { type: types.FINISHED_CHECKING_PASSWORD }
}

export function networkError() {
  return { type: types.PASSWORD_NETWORK_ERROR, exception_message: 'Network error' }
}
