import * as types from '../constants/ActionTypes'
import { post } from './common_actions'
import { generateNextRangeList } from "~/util/date";

export function setNextRangeList(next_start_ym) {
  if(!next_start_ym) return { type: types.SET_NEXT_RANGE_LIST, next_range_list: [] }
  const year = next_start_ym.substr(0, 4)
  const month = next_start_ym.substr(-2, 2)
  const next_start_date = new Date(parseInt(year), parseInt(month) - 1);
  const next_range_list = generateNextRangeList(next_start_date)
  return { type: types.SET_NEXT_RANGE_LIST, next_range_list }
}

export function changeToTimezoneSelectMode() {
  return { type: types.CHANGE_TO_TIMEZONE_EDIT_MODE }
}

export function changeToTimezoneNotSelectMode() {
  return { type: types.CHANGE_TO_TIMEZONE_NOT_EDIT_MODE }
}

export function checkingTerm() {
  return { type: types.CHECKING_TERM }
}

export function finishedCheckingTerm() {
  return { type: types.FINISHED_CHECKING_TERM }
}

export function exception(exception_message) {
  return { type: types.TERM_NETWORK_ERROR, exception_message }
}

export function postTerms(post_data) {
  return dispatch => {
    const data = {
      'data[Team][border_months]': post_data.term,
      'data[Team][timezone]': post_data.timezone,
      // TODO: must change data format
      //       integer -> string 'YYYY-mm'
      'data[Term][next_start_ym]': post_data.next_start_ym
    }

    dispatch(checkingTerm())
    return post('/signup/ajax_register_user', data, response => {
      const is_not_available = response.data.is_not_available
      const term_is_invlalid = response.data.error

      // 例外パターンのためSignupのトップページにリダイレクトさせる
      if(is_not_available || term_is_invlalid) {
        dispatch(exception(response.data.message))
        dispatch(finishedCheckingTerm())
        return redirectToTop()
      } else {
        document.location.href = "/teams/invite"
      }
    }, () => {
      dispatch(finishedCheckingTerm())
      dispatch(exception('Some error occurred'))
      return redirectToTop()
    })
  }
}

function redirectToTop() {
  // もう一回やり直す必要があるというメッセージをユーザに読んでもらうために
  // 3秒間スリープさせる
  setTimeout(() => {
    document.location.href = "/signup/email"
  }, 3000)
}