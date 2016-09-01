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

  it('initialize all invalid status', () => {
    expect(actions.initAllStatus()).toEqual({ type: types.INIT_ALL_STATUS })
  })

  it('initialize auth all code', () => {
    expect(actions.initAllCode()).toEqual({ type: types.INIT_AUTH_CODE })
  })

  it('input auth code 1st', () => {
    expect(actions.inputNewCode(1, 5)).toEqual({ type: types.INPUT_CODE, code_list: { 'code1': 5 } })
  })

  it('input auth code 2nd', () => {
    expect(actions.inputNewCode(2, 6)).toEqual({ type: types.INPUT_CODE, code_list: { 'code2': 6 } })
  })

  it('input auth code 3rd', () => {
    expect(actions.inputNewCode(3, 7)).toEqual({ type: types.INPUT_CODE, code_list: { 'code3': 7 } })
  })

  it('input auth code 4th', () => {
    expect(actions.inputNewCode(4, 3)).toEqual({ type: types.INPUT_CODE, code_list: { 'code4': 3 } })
  })

  it('input auth code 5th', () => {
    expect(actions.inputNewCode(5, 7)).toEqual({ type: types.INPUT_CODE, code_list: { 'code5': 7 } })
  })

  it('input auth code 6th', () => {
    expect(actions.inputNewCode(6, 8)).toEqual({ type: types.INPUT_CODE, code_list: { 'code6': 8 } })
  })

  it('checking auth code', () => {
    expect(actions.checkingAuthCode()).toEqual({ type: types.CHECKING_AUTH_CODE })
  })

  it('finish checking auth code', () => {
    expect(actions.finishedCheckingAuthCode()).toEqual({ type: types.FINISHED_CHECKING_AUTH_CODE })
  })

  it('lock auth', () => {
    expect(actions.authCodeIsLocked('locked message')).toEqual({ type: types.AUTH_CODE_IS_LOCKED, locked_message: 'locked message' })
  })

  it('expired auth', () => {
    expect(actions.authCodeIsExpired('expired message')).toEqual({ type: types.AUTH_CODE_IS_EXPIRED, expired_message: 'expired message' })
  })

  it('invalid auth', () => {
    expect(actions.authCodeIsInvalid('invalid message')).toEqual({ type: types.AUTH_CODE_IS_INVALID, invalid_message: 'invalid message' })
  })

  it('valid auth', () => {
    expect(actions.authCodeIsValid()).toEqual({ type: types.AUTH_CODE_IS_VALID })
  })

  it('get full code', () => {
    expect(actions.getFullCode({ code1: 3, code2: 5, code3: 1, code4: 0, code5: 9, code6: 2 })).toEqual(351092)
  })


  it('inputCode', () => {
    const expectedActions = [
      { type: types.INIT_ALL_STATUS },
      { type: types.INPUT_CODE, code_list: { 'code1': 3 } }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.inputCode(1, 3))
    expect(store.getActions()).toEqual(expectedActions)
  })


  it('post auth code locked test', () => {
    nock('http://localhost')
      .post('/signup/ajax_verify_code')
      .reply(200, { error: true, message: "auth code locked message", is_locked: true, is_expired: false })

    const expectedActions = [
      { type: types.CHECKING_AUTH_CODE },
      { type: types.FINISHED_CHECKING_AUTH_CODE },
      { type: types.AUTH_CODE_IS_LOCKED, locked_message: 'auth code locked message' },
      { type: types.INIT_AUTH_CODE }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postVerifyCode(123456))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('post auth code expired test', () => {
    nock('http://localhost')
      .post('/signup/ajax_verify_code')
      .reply(200, { error: true, message: "auth code expired message", is_locked: false, is_expired: true })

    const expectedActions = [
      { type: types.CHECKING_AUTH_CODE },
      { type: types.FINISHED_CHECKING_AUTH_CODE },
      { type: types.AUTH_CODE_IS_EXPIRED, expired_message: 'auth code expired message' },
      { type: types.INIT_AUTH_CODE }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postVerifyCode(123456))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('post auth code invalid test', () => {
    nock('http://localhost')
      .post('/signup/ajax_verify_code')
      .reply(200, { error: true, message: "auth code invalid message", is_locked: false, is_expired: false })

    const expectedActions = [
      { type: types.CHECKING_AUTH_CODE },
      { type: types.FINISHED_CHECKING_AUTH_CODE },
      { type: types.AUTH_CODE_IS_INVALID, invalid_message: 'auth code invalid message' },
      { type: types.INIT_AUTH_CODE }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postVerifyCode(123456))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

})
