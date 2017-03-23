import * as types from '~/message/constants/ActionTypes'

const initialState = {
  topics: [],
  topics_searched: [],
  next_url: '',
  fetching_topics: false
}

export default function topic(state = initialState, action) {
  switch (action.type) {
    case types.FETCHING_TOPICS:
      return Object.assign({}, state, {
        fetching_topics: true
      })
    case types.FETCH_TOPICS:
      return Object.assign({}, state, {
        topics: action.data.topics,
        next_url: action.data.next_url,
        fetching_topics: false
      })
    case types.FETCH_MORE_TOPICS:
      return Object.assign({}, state, {
        topics: [...state.topics, ...action.data.topics],
        next_url: action.data.next_url,
        fetching_topics: false
      })
    case types.FETCH_SEARCH_TOPICS:
      return Object.assign({}, state, {
        topics_searched: action.topics,
        fetching_topics: false
      })
    default:
      return state;
  }
}
