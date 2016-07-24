import * as types from '../constants/ActionTypes'
import {
  post,
  mapValidationMsg
} from './common_actions'

export function selectTerm(term) {
  return dispatch => {
    dispatch({
      type: types.SELECT_TERM,
      selected_term: term
    })
  }
}

export function selectStartMonth(start_month) {
  return dispatch => {
    dispatch({
      type: types.SELECT_START_MONTH,
      selected_start_month: start_month
    })
  }
}

export function selectTimezone(timezone) {
  return dispatch => {
    dispatch({
      type: types.SELECT_TIMEZONE,
      selected_timezone: timezone
    })
  }
}

export function changeToTimezoneSelectMode() {
  return dispatch => {
    dispatch({ type: types.CHANGE_TO_TIMEZONE_EDIT_MODE })
  }
}

export function changeToTimezoneNotSelectMode() {
  return dispatch => {
    dispatch({ type: types.CHANGE_TO_TIMEZONE_NOT_EDIT_MODE })
  }
}

export function enableSubmitButton() {
  return { type: types.CAN_SUBMIT_TERM }
}

export function disableSubmitButton() {
  return { type: types.CAN_NOT_SUBMIT_TERM }
}

export function postTerms(terms) {
  return dispatch => {
    dispatch({ type: types.CHECKING_TERM })
    const data = {
      'data[Team][border_months]': terms.term,
      'data[Team][timezone]': terms.timezone,
      'data[Team][start_term_month]': terms.start_month
    }

    return post('/signup/ajax_validation_fields', data, response => {
      const term_is_invlalid = response.data.error && Object.keys(response.data.validation_msg).length

      dispatch({ type: types.FINISHED_CHECKING_TERM })
      if (term_is_invlalid) {
        dispatch({
          type: types.TERM_IS_INVALID,
          invalid_messages: mapValidationMsg(response.data.validation_msg)
        })
      } else {
        dispatch({ type: types.TERM_IS_VALID })
      }
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_TERM })
      dispatch({
        type: types.TERM_NETWORK_ERROR,
        exception_message: 'Network error'
      })
    })
  }
}
