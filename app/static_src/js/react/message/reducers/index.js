import * as types from '~/message/constants/ActionTypes'

const initialState = {
  topics: [],
  topics_searched: []
}

export default function topic(state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
