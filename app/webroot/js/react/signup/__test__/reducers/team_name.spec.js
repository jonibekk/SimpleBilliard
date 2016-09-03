import teamNameReducer from '../../reducers/team_name'
import * as types from '../../constants/actionTypes'
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
})
