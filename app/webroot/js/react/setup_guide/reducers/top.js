import { FETCH_SETUP_STATUS } from '../constants/ActionTypes'

let initialState = {
  setup_status: {
    1: 0,
    2: 0,
    3: 0,
    4: 0,
    5: 0,
    6: 0
  },
  setup_rest_count: 0
}

export default function top(state = initialState, action) {
  switch (action.type) {
    case FETCH_SETUP_STATUS:
      return Object.assign({}, state, {
        status: action.status,
        setup_rest_count: action.setup_rest_count,
      })
    default:
      return state;
  }
}
