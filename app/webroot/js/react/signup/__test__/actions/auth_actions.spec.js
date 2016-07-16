import configureMockStore from 'redux-mock-store'
import thunk from 'redux-thunk'
import * as actions from '../../actions/auth_actions'
import * as types from '../../constants/ActionTypes'
import nock from 'nock'
import expect from 'expect'

const middlewares = [ thunk ]
const mockStore = configureMockStore(middlewares)

describe('actions::auth', () => {
  afterEach(() => {
    nock.cleanAll()
  })

  it('inputCode digit is less than 6', () => {
    const expectedActions = [
      { type: types.INPUT_CODE, inputed_code: 12345 }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.inputCode(12345))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('inputCode digit is 6', () => {
    const expectedActions = [
      { type: types.INPUT_CODE, inputed_code: 123456 }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.inputCode(123456))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('inputCode code is not digit', () => {
    const expectedActions = [
      { type: types.INIT_AUTH_CODE }
    ]
    const store = mockStore({ auth: [] })
    
    store.dispatch(actions.inputCode('abcdef'))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('postVerifyCode code is locked test', () => {
    nock('http://127.0.0.1')
      .post('/signup/ajax_verify_code')
      .reply(200, { error: true, message: "auth code locked message", is_locked: true, is_expired: false })

    const expectedActions = [
      { type: types.CHECKING_AUTH_CODE },
      { type: types.FINISHED_CHECKING_AUTH_CODE },
      { type: types.AUTH_CODE_IS_LOCKED, locked_message: 'auth code locked message' }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postVerifyCode(123456))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postVerifyCode code is expired test', () => {
    nock('http://127.0.0.1')
      .post('/signup/ajax_verify_code')
      .reply(200, { error: true, message: "auth code expired message", is_locked: false, is_expired: true })

    const expectedActions = [
      { type: types.CHECKING_AUTH_CODE },
      { type: types.FINISHED_CHECKING_AUTH_CODE },
      { type: types.AUTH_CODE_IS_EXPIRED, expired_message: 'auth code expired message' }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postVerifyCode(123456))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postVerifyCode code is invalid test', () => {
    nock('http://127.0.0.1')
      .post('/signup/ajax_verify_code')
      .reply(200, { error: true, message: "auth code invalid message", is_locked: false, is_expired: false })

    const expectedActions = [
      { type: types.CHECKING_AUTH_CODE },
      { type: types.FINISHED_CHECKING_AUTH_CODE },
      { type: types.AUTH_CODE_IS_INVALID, invalid_message: 'auth code invalid message' }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postVerifyCode(123456))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postVerifyCode code is valid test', () => {
    nock('http://127.0.0.1')
      .post('/signup/ajax_verify_code')
      .reply(200, { error: false, message: "", is_locked: false, is_expired: false })

    const expectedActions = [
      { type: types.CHECKING_AUTH_CODE },
      { type: types.FINISHED_CHECKING_AUTH_CODE },
      { type: types.AUTH_CODE_IS_VALID }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postVerifyCode(123456))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })
})
