import { SELECT_NO_DEVICES } from '../constants/ActionTypes'

const initialState = {
  select_no_devices: false
}

export default function action(state = initialState, action) {
  switch (action.type) {
    case SELECT_NO_DEVICES:
      return Object.assign({}, state, {
        select_no_devices: true
      })
    default:
      return state;
  }
}
