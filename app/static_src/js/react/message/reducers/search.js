import * as types from '~/message/constants/ActionTypes'

const initialState = {
  topics: [],
  next_url: '',
  fetching: false,
  current_searching_keyword: ''
}

export default function search(state = initialState, action) {
  switch (action.type) {
    case types.FETCHING_SEARCH:
      return Object.assign({}, state, {
        fetching: true
      })
    case types.SEARCH:
      return Object.assign({}, state, {
        topics: action.topics,
        next_url: action.next_url,
        fetching: false
      })
    case types.FETCH_MORE_SEARCH:
      return Object.assign({}, state, {
        topics: [...state.topics, ...action.data.topics],
        next_url: action.data.next_url,
        fetching: false
      })
    case types.INPUT_KEYWORD:
      return Object.assign({}, state, {
        inputed_search_keyword: action.keyword
      })
    case types.SET_SEARCHING_KEYWORD:
      return Object.assign({}, state, {
        current_searching_keyword: action.keyword,
        fetching: true,
        topics: []
      })
    case types.INITIALIZE_SEARCH:
      return Object.assign({}, state, initialState)
    case types.UPDATE_TOPIC_SEARCH_LIST_ITEM:
      if (typeof state.topics[action.index] == "undefined") {
        return state
      }
      const update_item = Object.assign(state.topics[action.index], action.data)
      const new_state = state.topics[action.index] = update_item
      return Object.assign({}, state, new_state)
    default:
      return state;
  }
}
