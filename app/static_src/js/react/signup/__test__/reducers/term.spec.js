import termReducer from '../../reducers/term'
import * as types from '../../constants/ActionTypes'
import expect from 'expect'

describe('reducer::term', () => {
  it('CHECKING_TERM', () => {
    const expect_value = {
      checking_term: true
    }
    const newState = termReducer({}, {
      type: types.CHECKING_TERM
    })

    expect(newState).toInclude(expect_value)
  })
  it('FINISHED_CHECKING_TERM', () => {
    const expect_value = {
      checking_term: false
    }
    const newState = termReducer({}, {
      type: types.FINISHED_CHECKING_TERM
    })

    expect(newState).toInclude(expect_value)
  })
  it('TERM_NETWORK_ERROR', () => {
    const expect_value = {
      is_exception: true,
      exception_message: 'term exception message'
    }
    const newState = termReducer({}, {
      type: types.TERM_NETWORK_ERROR,
      exception_message: 'term exception message'
    })

    expect(newState).toInclude(expect_value)
  })
  it('CHANGE_TO_TIMEZONE_NOT_EDIT_MODE', () => {
    const expect_value = {
      is_timezone_edit_mode: false
    }
    const newState = termReducer({}, {
      type: types.CHANGE_TO_TIMEZONE_NOT_EDIT_MODE
    })

    expect(newState).toInclude(expect_value)
  })
  it('CHANGE_TO_TIMEZONE_EDIT_MODE', () => {
    const expect_value = {
      is_timezone_edit_mode: true
    }
    const newState = termReducer({}, {
      type: types.CHANGE_TO_TIMEZONE_EDIT_MODE
    })

    expect(newState).toInclude(expect_value)
  })
  it('SET_START_MONTH_LIST', () => {
    const expect_value = {
      start_month_list: []
    }
    const newState = termReducer({}, {
      type: types.SET_START_MONTH_LIST,
      start_month_list: []
    })

    expect(newState).toInclude(expect_value)
  })
})
