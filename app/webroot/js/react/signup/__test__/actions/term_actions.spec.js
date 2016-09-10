import configureMockStore from 'redux-mock-store'
import thunk from 'redux-thunk'
import * as actions from '../../actions/term_actions'
import * as types from '../../constants/ActionTypes'
import nock from 'nock'
import expect from 'expect'

const middlewares = [ thunk ]
const mockStore = configureMockStore(middlewares)

describe('actions::term', () => {
  afterEach(() => {
    nock.cleanAll()
  })

  it('changeToTimezoneSelectMode', () => {
    expect(actions.changeToTimezoneSelectMode()).toEqual({ type: types.CHANGE_TO_TIMEZONE_EDIT_MODE })
  })

  it('changeToTimezoneNotSelectMode', () => {
    expect(actions.changeToTimezoneNotSelectMode()).toEqual({ type: types.CHANGE_TO_TIMEZONE_NOT_EDIT_MODE })
  })

  it('checkingTerm', () => {
    expect(actions.checkingTerm()).toEqual({ type: types.CHECKING_TERM })
  })

  it('finishedCheckingTerm', () => {
    expect(actions.finishedCheckingTerm()).toEqual({ type: types.FINISHED_CHECKING_TERM })
  })

  it('exception', () => {
    expect(actions.exception('message')).toEqual({ type: types.TERM_NETWORK_ERROR, exception_message: 'message' })
  })

})
