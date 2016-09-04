import passwordReducer from '../../reducers/password'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::password', () => {
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
