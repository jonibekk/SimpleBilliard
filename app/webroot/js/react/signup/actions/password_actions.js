import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import { post, mapValidationMsg } from './common_actions'

export function inputPassword(password) {
  return dispatch => {
    dispatch({ type: types.INPUT_PASSWORD, inputed_password: password })
    if(String(password).length >= 8) {
      dispatch(enableSubmitButton())
    } else {
      dispatch(disableSubmitButton())
    }
  }
}

export function enableSubmitButton() {
  return { type: types.CAN_SUBMIT_PASSWORD }
}

export function disableSubmitButton() {
  return { type: types.CAN_NOT_SUBMIT_PASSWORD }
}

export function invalid(messages) {
  return dispatch => {
    dispatch(disableSubmitButton())
    dispatch({
      type: types.PASSWORD_IS_INVALID,
      invalid_messages: messages
    })
  }
}

export function valid() {
  return dispatch => {
    dispatch(enableSubmitButton())
    dispatch({ type: types.PASSWORD_IS_VALID })
  }
}

export function postPassword(password) {
  return dispatch => {
    dispatch(disableSubmitButton())
    dispatch({ type: types.CHECKING_PASSWORD })

    return post('/signup/ajax_validation_fields', {'data[User][password]': password}, response => {
      const password_is_invlalid = response.data.error && Object.keys(response.data.validation_msg).length

      dispatch({ type: types.FINISHED_CHECKING_PASSWORD})
      if (password_is_invlalid) {
        dispatch(invalid(mapValidationMsg(response.data.validation_msg)))
      } else {
        dispatch(valid())
        browserHistory.push('/signup/team')
      }
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_PASSWORD })
      dispatch({
        type: types.PASSWORD_NETWORK_ERROR,
        exception_message: 'Network error'
      })
      dispatch(enableSubmitButton())
    })
  }
}
