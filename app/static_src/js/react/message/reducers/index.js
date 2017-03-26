import * as types from '~/message/constants/ActionTypes'

const initialState = {
  topics: [],
  topics_searched: [],
  next_url: '',
  next_search_url: '',
  fetching_topics: false,
  searching_topics: false,
  searching_keyword: '',
  inputed_search_keyword: '',
  is_search_mode: false
}

export default function topic(state = initialState, action) {
  switch (action.type) {
    case types.FETCHING_TOPICS:
      return Object.assign({}, state, {
        fetching_topics: true
      })
    case types.FETCHING_SEARCH_TOPICS:
      return Object.assign({}, state, {
        searching_topics: true
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
    case types.FETCH_MORE_SEARCH_TOPICS:
      return Object.assign({}, state, {
        topics_searched: [...state.topics_searched, ...action.data.topics_searched],
        next_search_url: action.data.next_search_url,
        searching_topics: false
      })
    case types.SEARCH_TOPICS:
      return Object.assign({}, state, {
        topics_searched: action.topics_searched,
        searching_topics: false,
        next_search_url: action.next_search_url
      })
    case types.INPUT_SEARCH_KEYWORD:
      return Object.assign({}, state, {
        inputed_search_keyword: action.keyword
      })
    case types.CHANGE_TO_SEARCH_MODE:
      return Object.assign({}, state, {
        is_search_mode: true
      })
    case types.CANCEL_SEARCH_MODE:
      return Object.assign({}, state, {
        is_search_mode: false
      })
    case types.SEARCH_KEYWORD:
      return Object.assign({}, state, {
        searching_keyword: action.keyword,
        searching_topics: true,
        topics_searched: []
      })
    default:
      return state;
  }
}
