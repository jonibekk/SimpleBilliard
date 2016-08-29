import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import {
  post,
  mapValidationMsg
} from './common_actions'

export function inputTeamName(team_name) {
  return dispatch => {
    dispatch({
      type: types.INPUT_TEAM_NAME,
      inputed_team_name: team_name
    })
    if(String(team_name).length > 0) {
      dispatch(enableSubmitButton())
    } else {
      dispatch(disableSubmitButton())
    }
  }
}

export function enableSubmitButton() {
  return { type: types.CAN_SUBMIT_TEAM_NAME }
}

export function disableSubmitButton() {
  return { type: types.CAN_NOT_SUBMIT_TEAM_NAME }
}

export function invalid(messages) {
  return dispatch => {
    dispatch(disableSubmitButton())
    dispatch({
      type: types.TEAM_NAME_IS_INVALID,
      invalid_messages: messages
    })
  }
}

export function valid() {
  return dispatch => {
    dispatch(enableSubmitButton())
    dispatch({ type: types.TEAM_NAME_IS_VALID, invalid_messages: {} })
  }
}

export function postTeamName(team_name) {
  return dispatch => {
    dispatch(disableSubmitButton())
    dispatch({ type: types.CHECKING_TEAM_NAME })
    const data = {
      'data[Team][name]': team_name
    }

    return post('/signup/ajax_validation_fields', data, response => {
      const team_name_is_invlalid = response.data.error && Object.keys(response.data.validation_msg).length

      dispatch({ type: types.FINISHED_CHECKING_TEAM_NAME })
      if (team_name_is_invlalid) {
        dispatch({
          type: types.TEAM_NAME_IS_INVALID,
          invalid_messages: mapValidationMsg(response.data.validation_msg)
        })
        dispatch(enableSubmitButton())
      } else {
        browserHistory.push('/signup/term')
        dispatch({ type: types.TEAM_NAME_IS_VALID })
      }
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_TEAM_NAME })
      dispatch({
        type: types.TEAM_NAME_NETWORK_ERROR,
        exception_message: 'Network error'
      })
      dispatch(enableSubmitButton())
    })
  }
}
