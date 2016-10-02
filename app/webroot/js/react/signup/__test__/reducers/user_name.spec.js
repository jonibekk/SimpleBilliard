import userNameReducer from '../../reducers/user_name'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::user_name', () => {
  it('USER_TO_NEXT_PAGE', () => {
    const expect_value = {
      to_next_page: 'next/page'
    }
    const newState = userNameReducer({}, {
      type: types.USER_TO_NEXT_PAGE,
      to_next_page: 'next/page'
    })

    expect(newState).toInclude(expect_value)
  })
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
  it('NETWORK_ERROR', () => {
    const expect_value = {
      is_exception: true,
      exception_message: 'network error'
    }
    const newState = userNameReducer({}, {
      type: types.USER_NETWORK_ERROR,
      exception_message: 'network error'
    })

    expect(newState).toInclude(expect_value)
  })
})
