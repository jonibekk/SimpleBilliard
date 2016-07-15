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
      { type: types.INPUT_USER_NAME, key: 'key_name', name: 'name' },
    ]
    const store = mockStore({ auth: [] })
    store.dispatch(actions.inputName('key_name', 'name'))
    expect(store.getActions()).toEqual(expectedActions)
  })

  // it('inputCode digit is less than 6', () => {
  //   const expectedActions = [
  //     { type: types.INPUT_CODE, inputed_code: 12345 },
  //   ]
  //   const store = mockStore({ auth: [] })
  //   store.dispatch(actions.inputCode(12345))
  //   expect(store.getActions()).toEqual(expectedActions)
  // })
})
