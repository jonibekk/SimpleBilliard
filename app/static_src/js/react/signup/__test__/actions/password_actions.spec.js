import configureMockStore from 'redux-mock-store'
import thunk from 'redux-thunk'
import * as actions from '../../actions/password_actions'
import * as types from '../../constants/ActionTypes'
import nock from 'nock'
import expect from 'expect'

const middlewares = [thunk]
const mockStore = configureMockStore(middlewares)

describe('actions::password', () => {
  afterEach(() => {
    nock.cleanAll()
  })

  it('checking password', () => {
    expect(actions.checkingPassword()).toEqual({ type: types.CHECKING_PASSWORD })
  })

  it('finished checking password', () => {
    expect(actions.finishedCheckingPassword()).toEqual({ type: types.FINISHED_CHECKING_PASSWORD })
  })

  it('finished checking password', () => {
    expect(actions.finishedCheckingPassword()).toEqual({ type: types.FINISHED_CHECKING_PASSWORD })
  })

  it('to password next page', () => {
    expect(actions.toNextPage('path/to/next')).toEqual({ type: types.PASSWORD_TO_NEXT_PAGE, to_next_page: 'path/to/next' })
  })

  it('postPassword success', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, { error: false, message: "", is_locked: false, is_expired: false })

    const expectedActions = [{
      type: types.CHECKING_PASSWORD
    }, {
      type: types.FINISHED_CHECKING_PASSWORD
    }, {
      type: types.PASSWORD_TO_NEXT_PAGE, to_next_page: '/signup/team'
    }]
    const store = mockStore({
      auth: []
    })

    return store.dispatch(actions.postPassword('Abcdefg123'))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postPassword network error', () => {
    nock('http://network-error')
      .post('/signup/ajax_validation_fields')
      .reply(200, {})

    const expectedActions = [{
      type: types.CHECKING_PASSWORD
    }, {
      type: types.FINISHED_CHECKING_PASSWORD
    }, {
      type: types.PASSWORD_NETWORK_ERROR,
      exception_message: 'Network error'
    }]
    const store = mockStore({
      auth: []
    })

    return store.dispatch(actions.postPassword('Abcdefg123'))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })
})
