import * as types from '../constants/ActionTypes'
import { post } from './common_actions'

export function selectTerm(term) {
  return (dispatch) => {
    dispatch({
      type: types.SELECT_TERM,
      selected_term: term
    })
    dispatch(updateStartMonthList())
    dispatch(toggleSubmitButton())
  }
}

export function selectStartMonth(start_month) {
  return (dispatch) => {
    dispatch({
      type: types.SELECT_START_MONTH,
      selected_start_month: start_month
    })
    dispatch(toggleSubmitButton())
  }
}

function toggleSubmitButton() {
  return (dispatch, getState) => {
    const state = getState()

    if(state.term.selected_term && state.term.selected_start_month) {
      dispatch(enableSubmitButton())
    } else {
      dispatch(disableSubmitButton())
    }
  }
}

function updateStartMonthList() {
  return (dispatch, getState) => {
    const state = getState()
    const selected_term = state.term.selected_term

    if(!selected_term) return false
    const date = new Date()
    const this_month = date.getMonth()
    const this_year = date.getFullYear()
    const start_month_select_list = []

    for(let i = parseInt(selected_term); i > 0; i--) {
      const start_month = new Date(this_year, this_month - (i - 1), 1)
      const formatted_start_date = dateFormat(start_month.getFullYear(), parseInt(start_month.getMonth()) + 1, 1)
      const end_month = new Date(this_year, (this_month - (i - 1)) + parseInt(selected_term), 0)
      const formatted_end_date = dateFormat(end_month.getFullYear(), parseInt(end_month.getMonth()) + 1, end_month.getDate())

      start_month_select_list.push({
        start_month: parseInt(start_month.getMonth()) + 1,
        range: `${formatted_start_date} - ${formatted_end_date}`
      })
    }
    dispatch({ type: types.SET_START_MONTH_LIST, start_month_list: start_month_select_list })
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

export function postTerms() {
  return (dispatch, getState) => {
    dispatch(disableSubmitButton())
    dispatch({ type: types.CHECKING_TERM })

    const state = getState()
    let data = {
      'data[Team][border_months]': state.term.selected_term,
      'data[Team][start_term_month]': state.term.selected_start_month
    }

    if(state.term.selected_timezone) {
      data['data[Team][timezone]'] = state.term.selected_timezone
    } else {
      // timezoneの選択が無い場合はデフォルトのtimezoneを登録する
      data['data[Team][timezone]'] = "+9.0"
    }

    return post('/signup/ajax_register_user', data, response => {
      dispatch({ type: types.FINISHED_CHECKING_TERM })

      const is_not_available = response.data.is_not_available
      const term_is_invlalid = Boolean(response.data.error && Object.keys(response.data.validation_msg).length)

      // 例外パターンのためSignupのトップページにリダイレクトさせる
      if(is_not_available || term_is_invlalid) {
        dispatch({
          type: types.TERM_NETWORK_ERROR,
          exception_message: response.data.message
        })
        redirectToTop()
      }

      dispatch({ type: types.TERM_IS_VALID })
      document.location.href = "/teams/invite"
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_TERM })
      dispatch({
        type: types.TERM_NETWORK_ERROR,
        exception_message: 'Network error'
      })
      dispatch(enableSubmitButton())
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
  if(month < 10) {
    month = "0" + String(parseInt(month))
  }
  if(day < 10) {
    day = "0" + String(parseInt(day))
  }
  return `${year}/${month}/${day}`
}
