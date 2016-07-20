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

  it('selectTerm', () => {
    const expectedActions = [
      { type: types.SELECT_TERM, selected_term: 1 }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.selectTerm(1))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('selectStartMonth', () => {
    const expectedActions = [
      { type: types.SELECT_START_MONTH, selected_start_month: 1 }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.selectStartMonth(1))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('selectTimeZone', () => {
    const expectedActions = [
      { type: types.SELECT_TIMEZONE, selected_timezone: 1 }
    ]
    const store = mockStore({ auth: [] })

    store.dispatch(actions.selectTimezone(1))
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('enableSubmitButton', () => {
    const expectedActions = [{ type: types.CAN_SUBMIT_TERM }]
    const store = mockStore({
      auth: []
    })

    store.dispatch(actions.enableSubmitButton())
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('disableSubmitButton', () => {
    const expectedActions = [{ type: types.CAN_NOT_SUBMIT_TERM}]
    const store = mockStore({
      auth: []
    })

    store.dispatch(actions.disableSubmitButton())
    expect(store.getActions()).toEqual(expectedActions)
  })

  it('postTerms invalid', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        "error": true,
        "message": "Invalid Data",
        "validation_msg": {
          "data[Team][timezone]": "timezone message",
          "data[Team][start_term_month]": "start_term_month message",
          "data[Team][border_months]": "border_months message"
        }
      })

    const expectedActions = [
      { type: types.CHECKING_TERM },
      { type: types.FINISHED_CHECKING_TERM },
      { type: types.TERM_IS_INVALID, invalid_messages: { timezone: 'timezone message', start_month: 'start_term_month message', term: 'border_months message' }}
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postTerms({ timezone: 1, start_month: 1, term: 1 }))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postTerms valid', () => {
    nock('http://localhost')
      .post('/signup/ajax_validation_fields')
      .reply(200, {
        "error": false,
        "message": "",
        "validation_msg": {}
      })

    const expectedActions = [
      { type: types.CHECKING_TERM },
      { type: types.FINISHED_CHECKING_TERM },
      { type: types.TERM_IS_VALID }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postTerms({ timezone: 1, start_month: 1, term: 1 }))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })

  it('postTerms network error', () => {
    nock('http://network-error')
      .post('/signup/ajax_validation_fields')
      .reply(200, {})

    const expectedActions = [
      { type: types.CHECKING_TERM },
      { type: types.FINISHED_CHECKING_TERM },
      { type: types.TERM_NETWORK_ERROR, exception_message: 'Network error' }
    ]
    const store = mockStore({ auth: [] })

    return store.dispatch(actions.postTerms({ timezone: 1, start_month: 1, term: 1 }))
      .then(() => {
        expect(store.getActions()).toEqual(expectedActions)
      })
  })
})
