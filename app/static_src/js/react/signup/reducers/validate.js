import * as types from '../constants/ActionTypes'

const defParams = {
  invalid: null,
  message: ''
}
const initialState = {
  first_name: defParams,
  last_name: defParams,
  birth_year: defParams,
  birth_month: defParams,
  birth_day: defParams,
  privacy_policy: defParams,
  password: defParams,
  team_name: defParams,
  term: defParams,
  next_start_ym: defParams,
  age: defParams
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
