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

  it('inputName', () => {
    const expectedActions = [
      { type: types.INPUT_USER_NAME, key: 'key_name', name: 'name' }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.inputName('key_name', 'name'))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('postUserName invalid', () => {
    nock('http://127.0.0.1')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        "error": true,
        "message": "Invalid Data",
        "validation_msg": {
          "data[User][first_name]": "first_name message",
          "data[User][last_name]": "last_name message",
          "data[Local][first_name]": "local_first_name message",
          "data[Local][last_name]": "local_last_name message"
        }
      })

    const expectedActions = [
      { type: types.CHECKING_USER_NAME },
      { type: types.FINISHED_CHECKING_USER_NAME },
      { type: types.USER_NAME_IS_INVALID, invalid_messages: {first_name: 'first_name message', last_name: 'last_name message', local_first_name: 'local_first_name message', local_last_name: 'local_last_name message'} }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postUserName({first_name: 'a', last_name: 'b', local_first_name: 'c', local_last_name: 'd'}))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postUserName valid', () => {
    nock('http://127.0.0.1')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        "error": false,
        "message": "",
        "validation_msg": {}
      })

    const expectedActions = [
      { type: types.CHECKING_USER_NAME },
      { type: types.FINISHED_CHECKING_USER_NAME },
      { type: types.USER_NAME_IS_VALID }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postUserName({first_name: 'a', last_name: 'b', local_first_name: 'c', local_last_name: 'd'}))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })
})
