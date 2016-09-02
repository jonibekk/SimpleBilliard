import * as types from '../constants/ActionTypes'

const initialState = {
  first_name: {
    invalid: null,
    message: ''
  },
  last_name: {
    invalid: null,
    message: ''
  },
  birth_year: {
    invalid: null,
    message: ''
  },
  birth_month: {
    invalid: null,
    message: ''
  },
  birth_day: {
    invalid: null,
    message: ''
  },
  privacy_policy: {
    invalid: null,
    message: ''
  },
  password: {
    invalid: null,
    message: ''
  },
  team_name: {
    invalid: null,
    message: ''
  },
  term: {
    invalid: null,
    message: ''
  },
  start_month: {
    invalid: null,
    message: ''
  }
}

export default function validate(state = initialState, action) {
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, action.data)
    case types.VALID:
      return Object.assign({}, state, action.data)
    default:
      return state;
  }
}
