import { FETCH_SETUP_STATUS } from '../constants/ActionTypes'

let initialState = {
  status: {
    1: false,
    2: false,
    3: false,
    4: false,
    5: false,
    6: false
  },
  setup_rest_count: 0,
  setup_complete_percent: 0
}

export default function top(state = initialState, action) {
  switch (action.type) {
    case FETCH_SETUP_STATUS:
      return Object.assign({}, state, {
        status: action.status,
        setup_rest_count: action.setup_rest_count,
        setup_complete_percent: action.setup_complete_percent
      })
    default:
      return state;
  }
}
