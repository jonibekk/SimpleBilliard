import authReducer from '../../reducers/auth'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::auth', () => {
  it('INPUT_CODE', () => {
    const expect_value = { inputed_code: 123456 }
    const newState = authReducer({}, { type: types.INPUT_CODE, inputed_code: 123456 })

    expect(newState).toInclude(expect_value)
  })

  it('CHECKING_AUTH_CODE', () => {
    const expect_value = { checking_auth_code: true }
    const newState = authReducer({}, { type: types.CHECKING_AUTH_CODE })

    expect(newState).toInclude(expect_value)
  })

  it('FINISHED_CHECKING_AUTH_CODE', () => {
    const expect_value = { checking_auth_code: false }
    const newState = authReducer({}, { type: types.FINISHED_CHECKING_AUTH_CODE })

    expect(newState).toInclude(expect_value)
  })

  it('INIT_AUTH_CODE', () => {
    const expect_value = { inputed_code: null }
    const newState = authReducer({}, { type: types.INIT_AUTH_CODE })

    expect(newState).toInclude(expect_value)
  })

  it('AUTH_CODE_IS_INVALID', () => {
    const expect_value = { auth_code_is_invalid: true, invalid_message: 'auth code is invalid' }
    const newState = authReducer({}, { type: types.AUTH_CODE_IS_INVALID, invalid_message: 'auth code is invalid' })

    expect(newState).toInclude(expect_value)
  })

  it('AUTH_CODE_IS_VALID', () => {
    const expect_value = { auth_code_is_invalid: false }
    const newState = authReducer({}, { type: types.AUTH_CODE_IS_VALID })

    expect(newState).toInclude(expect_value)
  })

  it('AUTH_CODE_IS_LOCKED', () => {
    const expect_value = { auth_code_is_locked: true, locked_message: 'auth code is locked' }
    const newState = authReducer({}, { type: types.AUTH_CODE_IS_LOCKED, locked_message: 'auth code is locked' })

    expect(newState).toInclude(expect_value)
  })

  it('AUTH_CODE_IS_UNLOCKED', () => {
    const expect_value = { auth_code_is_locked: false }
    const newState = authReducer({}, { type: types.AUTH_CODE_IS_UNLOCKED })

    expect(newState).toInclude(expect_value)
  })

  it('AUTH_CODE_IS_EXPIRED', () => {
    const expect_value = { auth_code_is_expired: true, expired_message: 'auth code expired message' }
    const newState = authReducer({}, { type: types.AUTH_CODE_IS_EXPIRED, expired_message: 'auth code expired message' })

    expect(newState).toInclude(expect_value)
  })

  it('AUTH_CODE_IS_ENABLED', () => {
    const expect_value = { auth_code_is_expired: false }
    const newState = authReducer({}, { type: types.AUTH_CODE_IS_ENABLED })

    expect(newState).toInclude(expect_value)
  })
})