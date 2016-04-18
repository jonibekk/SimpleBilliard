import * as ActionTypes from '../constants/ActionTypes'

let defaultState = {
  setup_status: {
    1: 0,
    2: 0,
    3: 0,
    4: 0,
    5: 0,
    6: 0
  },
  setup_rest_count: 0
};

export default function(state = defaultState, action) {
  switch (action.type) {
    case ActionTypes.SETUP_STATUS_UPDATE:
      return {state, setup_status: action.status};
    default:
      return state;
  }
}
