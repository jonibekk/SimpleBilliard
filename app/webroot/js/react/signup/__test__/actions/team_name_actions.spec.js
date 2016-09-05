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

  it('checking team name', () => {
    expect(actions.checkingTeamName()).toEqual({ type: types.CHECKING_TEAM_NAME })
  })

  it('finished checking team name', () => {
    expect(actions.finishedCheckingTeamName()).toEqual({ type: types.FINISHED_CHECKING_TEAM_NAME })
  })

  it('network error', () => {
    expect(actions.networkError()).toEqual({ type: types.TEAM_NAME_NETWORK_ERROR, exception_message: 'Network error' })
  })

  it('to team next page', () => {
    expect(actions.toNextPage('path/to/next')).toEqual({ type: types.TEAM_TO_NEXT_PAGE, to_next_page: 'path/to/next' })
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
