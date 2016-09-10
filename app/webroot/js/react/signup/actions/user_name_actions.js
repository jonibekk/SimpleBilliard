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
    return post('/signup/ajax_validation_fields', generatePostData(user), response => {
      dispatch(finishedCheckingUserName())
      const user_name_is_invlalid = Boolean(response.data.error && Object.keys(response.data.validation_msg).length)

      if (user_name_is_invlalid) {
        const error_messages = mapValidationMsg(response.data.validation_msg)

        for (const name in error_messages) {
          dispatch(invalid(name, error_messages[name]))
        }
      } else {
        dispatch(toNextPage('/signup/password'))
      }
    }, () => {
      dispatch(finishedCheckingUserName())
      dispatch(networkError())
    })
  }
}

export function generatePostData(user) {
  return {
    'data[User][first_name]': user.first_name,
    'data[User][last_name]': user.last_name,
    'data[User][birth_day]': `${user.birth_year}-${user.birth_month}-${user.birth_day}`,
    'data[User][update_email_flg]': user.update_email_flg ? 1 : 0,
    'data[User][local_date]': getLocalDate()
  }
}

export function toNextPage(to_next_page) {
  return {
    type: types.USER_TO_NEXT_PAGE,
    to_next_page
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
