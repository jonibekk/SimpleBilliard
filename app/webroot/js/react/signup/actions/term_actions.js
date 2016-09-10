import * as types from '../constants/ActionTypes'
import { post } from './common_actions'

export function setStartMonthList(selected_term) {
  if(!selected_term) return { type: types.SET_START_MONTH_LIST, start_month_list: [] }
  const date = new Date()
  const this_month = date.getMonth()
  const this_year = date.getFullYear()
  const start_month_list = []

  for(let i = parseInt(selected_term); i > 0; i--) {
    const start_month = new Date(this_year, this_month - (i - 1), 1)
    const formatted_start_date = dateFormat(start_month.getFullYear(), parseInt(start_month.getMonth()) + 1, 1)
    const end_month = new Date(this_year, (this_month - (i - 1)) + parseInt(selected_term), 0)
    const formatted_end_date = dateFormat(end_month.getFullYear(), parseInt(end_month.getMonth()) + 1, end_month.getDate())

    start_month_list.push({
      start_month: parseInt(start_month.getMonth()) + 1,
      range: `${formatted_start_date} - ${formatted_end_date}`
    })
  }
  return { type: types.SET_START_MONTH_LIST, start_month_list }
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
      'data[Team][start_term_month]': post_data.start_month,
      'data[Team][timezone]': post_data.timezone
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

function dateFormat(year, month, day) {
  const double_digit_month = month < 10 ? "0" + String(parseInt(month)) : month
  const translated_month_name = getMonthNameList()[double_digit_month]
  let month_name = ''

  if(translated_month_name.match(/月/)) {
    month_name = double_digit_month + '月'
  } else {
    month_name = translated_month_name
  }

  return `${month_name} ${day}, ${year}`
}

export function getMonthNameList() {
  return {
    "01": __("Jan"),
    "02": __("Feb"),
    "03": __("Mar"),
    "04": __("Apr"),
    "05": __("May"),
    "06": __("Jun"),
    "07": __("Jul"),
    "08": __("Aug"),
    "09": __("Sep"),
    "10": __("Oct"),
    "11": __("Nov"),
    "12": __("Dec")
  }
}
