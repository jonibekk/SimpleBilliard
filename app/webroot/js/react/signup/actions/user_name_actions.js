import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import {
  post,
  mapValidationMsg,
  getLocalDate
} from './common_actions'

export function enableSubmitButton() {
  return { type: types.CAN_SUBMIT_USER_NAME }
}

export function disableSubmitButton() {
  return { type: types.CAN_NOT_SUBMIT_USER_NAME }
}

export function invalid(element) {
  return dispatch => {
    dispatch(disableSubmitButton())
    dispatch({
      type: types.USER_NAME_IS_INVALID,
      invalid: element.invalid,
      invalid_messages: element.messages
    })
  }
}

export function valid(element) {
  return (dispatch, getState) => {
    dispatch({
      type: types.USER_NAME_IS_VALID,
      invalid: element.invalid,
      invalid_messages: element.messages
    })

    const invalid = getState().user_name.invalid

    for (const key in invalid) {
      if(invalid[key] !== false) return
    }
    dispatch({
      type: types.CAN_SUBMIT_USER_NAME
    })
  }
}

export function postUserName(user) {
  return dispatch => {
    dispatch(disableSubmitButton())
    dispatch({ type: types.CHECKING_USER_NAME })
    const data = {
      'data[User][first_name]': user.first_name,
      'data[User][last_name]': user.last_name,
      'data[User][birth_day]': `${user.birth_year}-${user.birth_month}-${user.birth_day}`,
      'data[User][update_email_flg]': user.update_email_flg ? 1 : 0,
      'data[User][local_date]': getLocalDate()
    }

    return post('/signup/ajax_validation_fields', data, response => {
      const user_name_is_invlalid = Boolean(response.data.error && Object.keys(response.data.validation_msg).length)

      dispatch({ type: types.FINISHED_CHECKING_USER_NAME })
      if (user_name_is_invlalid) {
        dispatch(enableSubmitButton())
        dispatch({
          type: types.USER_NAME_IS_INVALID,
          invalid_messages: mapValidationMsg(response.data.validation_msg)
        })
      } else {
        dispatch({ type: types.USER_NAME_IS_VALID })
        browserHistory.push('/signup/password')
        return
      }
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_USER_NAME })
      dispatch(enableSubmitButton())
      dispatch({
        type: types.USER_NETWORK_ERROR,
        exception_message: 'Network error'
      })
    })
  }
}
