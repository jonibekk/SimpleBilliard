import * as types from '~/message/constants/ActionTypes'

const initialState = {
  messages: []
}

export default function message(state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
