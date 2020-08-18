import configureMockStore from 'redux-mock-store'
import thunk from 'redux-thunk'
import * as actions from '../../actions/user_name_actions'
import * as types from '../../constants/ActionTypes'
import nock from 'nock'
import expect from 'expect'

const middlewares = [ thunk ]
const mockStore = configureMockStore(middlewares)

describe('actions::user_name', () => {
  afterEach(() => {
    nock.cleanAll()
  })

  it('checking user name', () => {
    expect(actions.checkingUserName()).toEqual({ type: types.CHECKING_USER_NAME })
  })

  it('finished checking user name', () => {
    expect(actions.finishedCheckingUserName()).toEqual({ type: types.FINISHED_CHECKING_USER_NAME })
  })

  it('to user next page', () => {
    expect(actions.toNextPage('path/to/next')).toEqual({ type: types.USER_TO_NEXT_PAGE, to_next_page: 'path/to/next' })
  })

  it('postUserName success', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, { error: false, message: "", validation_msg: {} })

    const expectedActions = [
      { type: types.CHECKING_USER_NAME },
      { type: types.FINISHED_CHECKING_USER_NAME },
      { type: types.USER_TO_NEXT_PAGE, to_next_page: '/signup/password' }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postUserName({ first_name: 'a', last_name: 'b', birth_day: '', update_email_flg: true }))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postUserName validation error', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, { error: true, message: "validation error", validation_msg: {
        'data[User][first_name]': 'first name invalid',
        'data[User][last_name]': 'last name invalid',
        'data[User][birth_day]': 'birth day invalid'
      }})

    const expectedActions = [
      { type: types.CHECKING_USER_NAME },
      { type: types.FINISHED_CHECKING_USER_NAME },
      { type: types.INVALID, data: { first_name: { invalid: true, message: 'first name invalid'} }},
      { type: types.INVALID, data: { last_name: { invalid: true, message: 'last name invalid'} }},
      { type: types.INVALID, data: { birth_day: { invalid: true, message: 'birth day invalid'} }}
    ]
    const store = mockStore({ user_name: [] })

    return store.dispatch(actions.postUserName({ first_name: 'a', last_name: 'b', birth_day: '', update_email_flg: true }))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postUserName network error', () => {
    nock('http://network-error')
      .post('/signup/ajax_validation_fields')
      .reply(200, {})

    const expectedActions = [
      { type: types.CHECKING_USER_NAME },
      { type: types.FINISHED_CHECKING_USER_NAME },
      { type: types.USER_NETWORK_ERROR, exception_message: 'Network error' }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postUserName({ first_name: 'a', last_name: 'b', birth_day: '', update_email_flg: true }))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })
})