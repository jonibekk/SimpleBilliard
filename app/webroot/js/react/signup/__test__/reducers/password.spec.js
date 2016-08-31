import passwordReducer from '../../reducers/password'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::password', () => {
  it('CAN_SUBMIT_PASSWORD', () => {
    const expect_value = {
      submit_button_is_enabled: true
    }
    const newState = passwordReducer({}, {
      type: types.CAN_SUBMIT_PASSWORD
    })

    expect(newState).toInclude(expect_value)
  })
  it('CAN_NOT_SUBMIT_PASSWORD', () => {
    const expect_value = {
      submit_button_is_enabled: false
    }
    const newState = passwordReducer({}, {
      type: types.CAN_NOT_SUBMIT_PASSWORD
    })

    expect(newState).toInclude(expect_value)
  })
  it('PASSWORD_IS_VALID', () => {
    const expect_value = {
      invalid: {
        password: false
      },
      invalid_messages: {
        password: ''
      }
    }
    const newState = passwordReducer({}, {
      type: types.PASSWORD_IS_VALID,
      invalid: {
        password: false
      },
      invalid_messages: {
        password: ''
      }
    })

    expect(newState).toInclude(expect_value)
  })
  it('PASSWORD_IS_INVALID', () => {
    const expect_value = {
      invalid: {
        password: true
      },
      invalid_messages: {
        password: 'password is invalid'
      }
    }
    const newState = passwordReducer({}, {
      type: types.PASSWORD_IS_INVALID,
      invalid: {
        password: true
      },
      invalid_messages: {
        password: 'password is invalid'
      }
    })

    expect(newState).toInclude(expect_value)
  })
  it('CHECKING_PASSWORD', () => {
    const expect_value = {
      checking_password: true
    }
    const newState = passwordReducer({}, {
      type: types.CHECKING_PASSWORD
    })

    expect(newState).toInclude(expect_value)
  })
  it('FINISHED_CHECKING_USER_NAME', () => {
    const expect_value = {
      checking_password: false
    }
    const newState = passwordReducer({}, {
      type: types.FINISHED_CHECKING_PASSWORD
    })

    expect(newState).toInclude(expect_value)
  })
  it('PASSWORD_NETWORK_ERROR', () => {
    const expect_value = {
      is_exception: true,
      exception_message: 'exception message'
    }
    const newState = passwordReducer({}, {
      type: types.PASSWORD_NETWORK_ERROR,
      exception_message: 'exception message'
    })

    expect(newState).toInclude(expect_value)
  })
})
