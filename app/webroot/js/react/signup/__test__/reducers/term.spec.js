import termReducer from '../../reducers/term'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::term', () => {
  it('SELECT_TERM', () => {
    const expect_value = {
      selected_term: 1
    }
    const newState = termReducer({}, {
      type: types.SELECT_TERM,
      selected_term: 1
    })

    expect(newState).toInclude(expect_value)
  })
  it('SELECT_START_MONTH', () => {
    const expect_value = {
      selected_start_month: 1
    }
    const newState = termReducer({}, {
      type: types.SELECT_START_MONTH,
      selected_start_month: 1
    })

    expect(newState).toInclude(expect_value)
  })
  it('SELECT_TIMEZONE', () => {
    const expect_value = {
      selected_timezone: 1
    }
    const newState = termReducer({}, {
      type: types.SELECT_TIMEZONE,
      selected_timezone: 1
    })

    expect(newState).toInclude(expect_value)
  })
  it('CHECKING_TERM', () => {
    const expect_value = {
      checking_term: true
    }
    const newState = termReducer({}, {
      type: types.CHECKING_TERM
    })

    expect(newState).toInclude(expect_value)
  })
  it('FINISHED_CHECKING_TERM', () => {
    const expect_value = {
      checking_term: false
    }
    const newState = termReducer({}, {
      type: types.FINISHED_CHECKING_TERM
    })

    expect(newState).toInclude(expect_value)
  })
  it('CAN_SUBMIT_TERM', () => {
    const expect_value = {
      submit_button_is_enabled: true
    }
    const newState = termReducer({}, {
      type: types.CAN_SUBMIT_TERM
    })

    expect(newState).toInclude(expect_value)
  })
  it('CAN_NOT_SUBMIT_TERM', () => {
    const expect_value = {
      submit_button_is_enabled: false
    }
    const newState = termReducer({}, {
      type: types.CAN_NOT_SUBMIT_TERM
    })

    expect(newState).toInclude(expect_value)
  })
  it('TERM_IS_VALID', () => {
    const expect_value = {
      invalid: {
        term: false
      },
      invalid_messages: {
        term: ''
      }
    }
    const newState = termReducer({}, {
      type: types.TERM_IS_VALID,
      invalid: {
        term: false
      },
      invalid_messages: {
        term: ''
      }
    })

    expect(newState).toInclude(expect_value)
  })
  it('TERM_IS_INVALID', () => {
    const expect_value = {
      invalid: {
        term: true
      },
      invalid_messages: {
        term: 'term is invalid'
      }
    }
    const newState = termReducer({}, {
      type: types.TERM_IS_VALID,
      invalid: {
        term: true
      },
      invalid_messages: {
        term: 'term is invalid'
      }
    })

    expect(newState).toInclude(expect_value)
  })
  it('TERM_NETWORK_ERROR', () => {
    const expect_value = {
      is_exception: true,
      exception_message: 'term exception message'
    }
    const newState = termReducer({}, {
      type: types.TERM_NETWORK_ERROR,
      exception_message: 'term exception message'
    })

    expect(newState).toInclude(expect_value)
  })
  it('CHANGE_TO_TIMEZONE_NOT_EDIT_MODE', () => {
    const expect_value = {
      is_timezone_edit_mode: false
    }
    const newState = termReducer({}, {
      type: types.CHANGE_TO_TIMEZONE_NOT_EDIT_MODE
    })

    expect(newState).toInclude(expect_value)
  })
  it('CHANGE_TO_TIMEZONE_EDIT_MODE', () => {
    const expect_value = {
      is_timezone_edit_mode: true
    }
    const newState = termReducer({}, {
      type: types.CHANGE_TO_TIMEZONE_EDIT_MODE
    })

    expect(newState).toInclude(expect_value)
  })
})
