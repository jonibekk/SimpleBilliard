import * as types from '../constants/ActionTypes'
import {
  post,
  mapValidationMsg
} from './common_actions'
import {
  invalid
} from './validate_actions'

export function postTeamName(team_name) {
  return dispatch => {
    dispatch(checkingTeamName())
    return post('/signup/ajax_validation_fields', { 'data[Team][name]': team_name }, response => {
      const team_name_is_invlalid = response.data.error && Object.keys(response.data.validation_msg).length

      dispatch(finishedCheckingTeamName())
      if (team_name_is_invlalid) {
        const error_messages = mapValidationMsg(response.data.validation_msg)

        for (const name in error_messages) {
          dispatch(invalid(name, error_messages[name]))
        }
      } else {
        dispatch(toNextPage('/signup/term'))
      }
    }, () => {
      dispatch(finishedCheckingTeamName())
      dispatch(networkError())
    })
  }
}

export function toNextPage(to_next_page) {
  return {
    type: types.TEAM_TO_NEXT_PAGE,
    to_next_page
  }
}

export function checkingTeamName() {
  return { type: types.CHECKING_TEAM_NAME }
}

export function finishedCheckingTeamName() {
  return { type: types.FINISHED_CHECKING_TEAM_NAME }
}

export function networkError() {
  return { type: types.TEAM_NAME_NETWORK_ERROR, exception_message: 'Network error' }
}
