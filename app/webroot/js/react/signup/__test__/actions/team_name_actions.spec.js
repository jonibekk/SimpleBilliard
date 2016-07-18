import configureMockStore from 'redux-mock-store'
import thunk from 'redux-thunk'
import * as actions from '../../actions/team_name_actions'
import * as types from '../../constants/ActionTypes'
import nock from 'nock'
import expect from 'expect'

const middlewares = [ thunk ]
const mockStore = configureMockStore(middlewares)

describe('actions::team_name', () => {
  afterEach(() => {
    nock.cleanAll()
  })

  it('inputTeamName', () => {
    const expectedActions = [
      { type: types.INPUT_TEAM_NAME, inputed_team_name: 'inputed team name' }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.inputTeamName('inputed team name'))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('inputTeamName submit button enable', () => {
    const expectedActions = [
      { type: types.CAN_SUBMIT_TEAM_NAME }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.enableSubmitButton())
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('inputTeamName submit button disabled', () => {
    const expectedActions = [
      { type: types.CAN_NOT_SUBMIT_TEAM_NAME }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.disableSubmitButton())
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('postTeamName invalid', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        "error": true,
        "message": "Invalid Data",
        "validation_msg": {
          "data[Team][name]": "team_name message"
        }
      })

    const expectedActions = [
      { type: types.CHECKING_TEAM_NAME },
      { type: types.FINISHED_CHECKING_TEAM_NAME },
      { type: types.TEAM_NAME_IS_INVALID, invalid_messages: {team_name: 'team_name message'} }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postTeamName('team_name'))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postTeamName valid', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        "error": false,
        "message": "",
        "validation_msg": {}
      })

    const expectedActions = [
      { type: types.CHECKING_TEAM_NAME },
      { type: types.FINISHED_CHECKING_TEAM_NAME },
      { type: types.TEAM_NAME_IS_VALID }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postTeamName('team_name'))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postTeamName network error', () => {
    nock('http://network-error')
      .post('/signup/ajax_validation_fields')
      .reply(200, {})

    const expectedActions = [
      { type: types.CHECKING_TEAM_NAME },
      { type: types.FINISHED_CHECKING_TEAM_NAME },
      { type: types.TEAM_NAME_NETWORK_ERROR, exception_message: 'Network error' }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postTeamName('team_name'))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })
})
