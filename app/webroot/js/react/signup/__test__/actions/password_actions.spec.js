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

  it('inputPassword', () => {
    const expectedActions = [{
      type: types.INPUT_PASSWORD,
      inputed_password: 'Abcdefg123'
    }]
    const store = mockStore({
      auth: []
    })

    store.dispatch(actions.inputPassword('Abcdefg123'))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('enableSubmitButton', () => {
    const expectedActions = [{ type: types.CAN_SUBMIT_PASSWORD }]
    const store = mockStore({
      auth: []
    })

    store.dispatch(actions.enableSubmitButton())
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('disableSubmitButton', () => {
    const expectedActions = [{ type: types.CAN_NOT_SUBMIT_PASSWORD }]
    const store = mockStore({
      auth: []
    })

    store.dispatch(actions.disableSubmitButton())
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('postPassword is invalid', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        error: true,
        message: "invlid",
        "validation_msg": {
          "data[User][password]": "password invalid message"
        }
      })

    const expectedActions = [{
      type: types.CHECKING_PASSWORD
    }, {
      type: types.FINISHED_CHECKING_PASSWORD
    }, {
      type: types.PASSWORD_IS_INVALID,
      invalid_messages: {
        password: 'password invalid message'
      }
    }]
    const store = mockStore({
      auth: []
    })

    return store.dispatch(actions.postPassword('Abcdefg123'))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postPassword is valid', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        error: false,
        message: "",
        validation_msg: ""
      })

    const expectedActions = [{
      type: types.CHECKING_PASSWORD
    }, {
      type: types.FINISHED_CHECKING_PASSWORD
    }, {
      type: types.PASSWORD_IS_VALID
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
