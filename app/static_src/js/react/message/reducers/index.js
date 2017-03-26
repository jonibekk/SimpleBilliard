import * as types from '~/message/constants/ActionTypes'

const initialState = {
  topics: [],
  next_url: '',
  fetching: false,
  is_search_mode: false
}

export default function topic(state = initialState, action) {
  switch (action.type) {
    case types.FETCHING:
      return Object.assign({}, state, {
        fetching: true
      })
    case types.INITIALIZE:
      return Object.assign({}, state, {
        topics: action.data.topics,
        next_url: action.data.next_url,
        fetching: false
      })
    case types.FETCH_MORE_TOPICS:
      return Object.assign({}, state, {
        topics: [...state.topics, ...action.data.topics],
        next_url: action.data.next_url,
        fetching: false
      })
    case types.CHANGE_TO_SEARCH_MODE:
      return Object.assign({}, state, {
        is_search_mode: true
      })
    case types.CHANGE_TO_INDEX_MODE:
      return Object.assign({}, state, {
        is_search_mode: false
      })
    default:
      return state;
  }
}
