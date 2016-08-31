import userNameReducer from '../../reducers/user_name'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::user_name', () => {
  it('CHECKING_USER_NAME', () => {
    const expect_value = {
      checking_user_name: true
    }
    const newState = userNameReducer({}, {
      type: types.CHECKING_USER_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('FINISHED_CHECKING_USER_NAME', () => {
    const expect_value = {
      checking_user_name: false
    }
    const newState = userNameReducer({}, {
      type: types.FINISHED_CHECKING_USER_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('USER_NAME_IS_INVALID', () => {
    const expect_value = {
      invalid: {
        user_name: true
      },
      invalid_messages: {
        user_name: 'invalid message'
      }
    }
    const newState = userNameReducer({}, {
      type: types.USER_NAME_IS_INVALID,
      invalid: {
        user_name: true
      },
      invalid_messages: {
        user_name: 'invalid message'
      }
    })

    expect(newState).toInclude(expect_value)
  })
  it('USER_NAME_IS_VALID', () => {
    const expect_value = {
      invalid: {
        user_name: false
      },
      invalid_messages: {
        user_name: ''
      }
    }
    const newState = userNameReducer({}, {
      type: types.USER_NAME_IS_VALID,
      invalid: {
        user_name: false
      },
      invalid_messages: {
        user_name: ''
      }
    })

    expect(newState).toInclude(expect_value)
  })
  it('CAN_SUBMIT_USER_NAME', () => {
    const expect_value = {
      submit_button_is_enabled: true
    }
    const newState = userNameReducer({}, {
      type: types.CAN_SUBMIT_USER_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('CAN_NOT_SUBMIT_USER_NAME', () => {
    const expect_value = {
      submit_button_is_enabled: false
    }
    const newState = userNameReducer({}, {
      type: types.CAN_NOT_SUBMIT_USER_NAME
    })

    expect(newState).toInclude(expect_value)
  })
  it('NETWORK_ERROR', () => {
    const expect_value = {
      is_exception: true,
      exception_message: 'exception message'
    }
    const newState = userNameReducer({}, {
      type: types.USER_NETWORK_ERROR,
      exception_message: 'exception message'
    })

    expect(newState).toInclude(expect_value)
  })
})
