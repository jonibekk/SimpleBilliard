import * as types from '../constants/ActionTypes'
import { post } from './common_actions'

export function selectTerm(term) {
  return (dispatch) => {
    dispatch({
      type: types.SELECT_TERM,
      selected_term: term
    })
    if(!term) {
      dispatch(invalid({
        invalid: { term: true },
        invalid_messages: { term: cake.message.validate.signup_term_required }
      }))
    } else {
      dispatch(valid({
        invalid: { term: false },
        invalid_messages: { term: '' }
      }))
    }
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
    if(!start_month) {
      dispatch(invalid({
        invalid: { start_month: true },
        invalid_messages: { start_month: cake.message.validate.signup_start_month_required }
      }))
    } else {
      dispatch(valid({
        invalid: { start_month: false },
        invalid_messages: { start_month: '' }
      }))
    }
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

export function invalid(valid_obj) {
  return dispatch => {
    dispatch(disableSubmitButton())
    dispatch({
      type: types.TERM_IS_INVALID,
      invalid: valid_obj.invalid,
      invalid_messages: valid_obj.invalid_messages
    })
  }
}

export function valid(valid_obj) {
  return dispatch => {
    dispatch({
      type: types.TERM_IS_VALID,
      invalid: valid_obj.invalid,
      invalid_messages: valid_obj.invalid_messages
    })
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
    dispatch({ type: types.SELECT_START_MONTH, selected_start_month: ''})
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
    const data = {
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
      const is_not_available = response.data.is_not_available
      const term_is_invlalid = response.data.error

      // 例外パターンのためSignupのトップページにリダイレクトさせる
      if(is_not_available || term_is_invlalid) {
        dispatch({
          type: types.TERM_NETWORK_ERROR,
          exception_message: response.data.message
        })
        dispatch({ type: types.FINISHED_CHECKING_TERM })
        return redirectToTop()
      }

      document.location.href = "/teams/invite"
    }, () => {
      dispatch({ type: types.FINISHED_CHECKING_TERM })
      dispatch({
        type: types.TERM_NETWORK_ERROR,
        exception_message: 'Some error occurred'
      })
      redirectToTop()
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
