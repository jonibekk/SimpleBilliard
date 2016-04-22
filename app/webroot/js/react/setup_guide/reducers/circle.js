import { FETCH_SETUP_STATUS } from '../constants/ActionTypes'

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

function messageModal(state = defaultState, action) {
  const { type, title, message } = action;

  switch (type) {
    case ActionTypes.FETCH_SETUP_STATUS:
      return {
        visibility: "show",
        title: title,
        message: message
      };

    default:
      return state;
  }
