import * as types from '~/message/constants/ActionTypes'

const initialState = {
  hit_count: 0,
  topics: [],
  next_url: '',
  fetching: false,
  changed_search_conditions: false,
  search_conditions: {
    keyword: '',
    type: "topic"
  },
  is_mobile_app: false,
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
        changed_search_conditions: false,
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
    case types.UPDATE_SEARCH_CONDITION:
      return Object.assign({}, state, {
        search_conditions: action.search_conditions,
        changed_search_conditions: true,
        fetching: true,
        topics: []
      })
    case types.INITIALIZE_SEARCH:
      console.log('types.INITIALIZE_SEARCH');
      const is_mobile_app = state.is_mobile_app
      return Object.assign({}, state, initialState, {
        is_mobile_app,
        search_conditions: {
          keyword: "",
          type: 'topic'
        }
      })
    case types.SET_UA_INFO:
      return Object.assign({}, state, {
        is_mobile_app: action.is_mobile_app
      })
    default:
      return state;
  }
}
