import teamNameReducer from '../../reducers/team_name'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::team_name', () => {
  it('INPUT_TEAM_NAME', () => {
    const expect_value = {
      inputed_team_name: 'あいうえお'
    }
    const newState = teamNameReducer({}, {
      type: types.INPUT_TEAM_NAME,
      inputed_team_name: 'あいうえお'
    })

    expect(newState).toInclude(expect_value)
  })
  it('CAN_SUBMIT_TEAM_NAME', () => {
    const expect_value = {
      submit_button_is_enabled: true
    }
    const newState = teamNameReducer({}, {
      type: types.CAN_SUBMIT_TEAM_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('CAN_NOT_SUBMIT_TEAM_NAME', () => {
    const expect_value = {
      submit_button_is_enabled: false
    }
    const newState = teamNameReducer({}, {
      type: types.CAN_NOT_SUBMIT_TEAM_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('TEAM_NAME_IS_INVALID', () => {
    const expect_value = {
      team_name_is_invalid: true,
      invalid_messages: {
        team_name: 'あいうえお'
      }
    }
    const newState = teamNameReducer({}, {
      type: types.TEAM_NAME_IS_INVALID,
      invalid_messages: {
        team_name: 'あいうえお'
      }
    })

    expect(newState).toInclude(expect_value)
  })
  it('TEAM_NAME_IS_VALID', () => {
    const expect_value = {
      team_name_is_invalid: false
    }
    const newState = teamNameReducer({}, {
      type: types.TEAM_NAME_IS_VALID
    })

    expect(newState).toInclude(expect_value)
  })
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
