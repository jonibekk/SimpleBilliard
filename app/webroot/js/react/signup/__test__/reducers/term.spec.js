import termReducer from '../../reducers/term'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::term', () => {
  it('SELECT_TERM', () => {
    const expect_value = { selected_term: 1 }
    const newState = termReducer({}, { type: types.SELECT_TERM, selected_term: 1 })

    expect(newState).toInclude(expect_value)
  })
  it('SELECT_START_MONTH', () => {
    const expect_value = { selected_start_month: 1 }
    const newState = termReducer({}, { type: types.SELECT_START_MONTH, selected_start_month: 1 })

    expect(newState).toInclude(expect_value)
  })
  it('SELECT_TIMEZONE', () => {
    const expect_value = { selected_time_zone: 1 }
    const newState = termReducer({}, { type: types.SELECT_TIMEZONE, selected_time_zone: 1 })

    expect(newState).toInclude(expect_value)
  })
  it('CHECKING_SELECTED_TERM', () => {
    const expect_value = { checking_term: true }
    const newState = termReducer({}, { type: types.CHECKING_SELECTED_TERM})

    expect(newState).toInclude(expect_value)
  })

})
