import passwordReducer from '../../reducers/password'
import * as types from '../../constants/ActionTypes'
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
  it('PASSWORD_TO_NEXT_PAGE', () => {
    const expect_value = {
      to_next_page: 'path/to/next'
    }
    const newState = passwordReducer({}, {
      type: types.PASSWORD_TO_NEXT_PAGE,
      to_next_page: 'path/to/next'
    })

    expect(newState).toInclude(expect_value)
  })
})
