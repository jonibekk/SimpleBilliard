import { browserHistory } from 'react-router'
import * as types from '../constants/ActionTypes'
import {
  post,
  mapValidationMsg,
  getLocalDate
} from './common_actions'
import {
  invalid
} from './validate_actions'

export function postUserName(user) {
  return dispatch => {
    dispatch(checkingUserName())
    const data = {
      'data[User][first_name]': user.first_name,
      'data[User][last_name]': user.last_name,
      'data[User][birth_day]': `${user.birth_year}-${user.birth_month}-${user.birth_day}`,
      'data[User][update_email_flg]': user.update_email_flg ? 1 : 0,
      'data[User][local_date]': getLocalDate()
    }

    return post('/signup/ajax_validation_fields', data, response => {
      const user_name_is_invlalid = Boolean(response.data.error && Object.keys(response.data.validation_msg).length)

      dispatch(finishedCheckingUserName())
      if (user_name_is_invlalid) {
        invalid(mapValidationMsg(response.data.validation_msg))
      } else {
        browserHistory.push('/signup/password')
      }
    }, () => {
      dispatch(finishedCheckingUserName())
      dispatch(networkError())
    })
  }
}

export function checkingUserName() {
  return { type: types.CHECKING_USER_NAME }
}

export function finishedCheckingUserName() {
  return { type: types.FINISHED_CHECKING_USER_NAME }
}

export function networkError() {
  return { type: types.USER_NETWORK_ERROR, exception_message: 'Network error' }
}
