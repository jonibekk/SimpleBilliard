import passwordReducer from '../../reducers/password'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::password', () => {
  it('INPUT_PASSWORD', () => {
    const expect_value = { inputed_password: 123456 }
    const newState = passwordReducer({}, { type: types.INPUT_PASSWORD, inputed_password: 123456})

    expect(newState).toInclude(expect_value)
  })
  it('PASSWORD_IS_INVALID', () => {
    const expect_value = { password_is_invalid: true }
    const newState = passwordReducer({}, { type: types.PASSWORD_IS_INVALID})

    expect(newState).toInclude(expect_value)
  })
  it('CHECKING_PASSWORD', () => {
    const expect_value = { checking_password: true }
    const newState = passwordReducer({}, { type: types.CHECKING_PASSWORD})

    expect(newState).toInclude(expect_value)
  })
  it('FINISHED_CHECKING_USER_NAME', () => {
    const expect_value = { checking_password: false }
    const newState = passwordReducer({}, { type: types.FINISHED_CHECKING_PASSWORD})

    expect(newState).toInclude(expect_value)
  })
  it('NETWORK_ERROR', () => {
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
