import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import {
  post,
  mapValidationMsg
} from './common_actions'

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
