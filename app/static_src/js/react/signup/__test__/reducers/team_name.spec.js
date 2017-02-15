import teamNameReducer from '../../reducers/team_name'
import * as types from '../../constants/ActionTypes'
import expect from 'expect'

describe('reducer::team_name', () => {
  it('CHECKING_TEAM_NAME', () => {
    const expect_value = {
      checking_team_name: true
    }
    const newState = teamNameReducer({}, {
      type: types.CHECKING_TEAM_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('FINISHED_CHECKING_TEAM_NAME', () => {
    const expect_value = {
      checking_team_name: false
    }
    const newState = teamNameReducer({}, {
      type: types.FINISHED_CHECKING_TEAM_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('NETWORK_ERROR', () => {
    const expect_value = {
      is_exception: true,
      exception_message: 'exception message'
    }
    const newState = teamNameReducer({}, {
      type: types.TEAM_NAME_NETWORK_ERROR,
      exception_message: 'exception message'
    })

    expect(newState).toInclude(expect_value)
  })
  it('TEAM_TO_NEXT_PAGE', () => {
    const expect_value = {
      to_next_page: 'path/to/next'
    }
    const newState = teamNameReducer({}, {
      type: types.TEAM_TO_NEXT_PAGE,
      to_next_page: 'path/to/next'
    })

    expect(newState).toInclude(expect_value)
  })
})
