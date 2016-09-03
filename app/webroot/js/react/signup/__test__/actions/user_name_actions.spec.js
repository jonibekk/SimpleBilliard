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

    return store.dispatch(actions.postUserName({first_name: 'a', last_name: 'b', local_first_name: 'c', local_last_name: 'd'}))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })
})
