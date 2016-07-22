import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import {
  post,
  mapValidationMsg,
  getLocalDate
} from './common_actions'

export function inputUserName(user) {
  return (dispatch, getState) => {
    dispatch({
      type: types.INPUT_USER_NAME,
      inputed: user
    })
    const state = getState()

    if(state.user_name.inputed.first_name && state.user_name.inputed.last_name && state.user_name.inputed.privacy) {
      dispatch(enableSubmitButton())
    } else {
      dispatch(disableSubmitButton())
    }
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
    dispatch(disableSubmitButton())
    dispatch({ type: types.INIT_USER_STATUS })
    dispatch({ type: types.CHECKING_USER_NAME })
    const data = {
      'data[User][first_name]': user.first_name,
      'data[User][last_name]': user.last_name,
      'data[User][local_date]': getLocalDate()
    }

    return post('/signup/ajax_validation_fields', data, response => {
      const user_name_is_invlalid = response.data.error && Object.keys(response.data.validation_msg).length

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
