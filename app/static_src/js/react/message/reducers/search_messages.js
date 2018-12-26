import * as types from '~/message/constants/ActionTypes'

const initialState = {
  topic_id: 0,
  topic: {},
  messages: [],
  next_url: '',
  search_total_count: 0,
  fetching: false,
  is_mobile_app: false,
  mobile_app_layout: {
    header_top: null,
    body_top: null,
    body_bottom: null,
    footer_bottom: null,
  },
  search_conditions: {
    keyword: ""
  }
}

export default function search_messages(state = initialState, action) {
  switch (action.type) {
    case types.SET_RESOURCE_ID:
      return Object.assign({}, state, {
        topic_id: action.topic_id
      });
    case types.FETCHING:
      return Object.assign({}, state, {
        fetching: true
      })
    case types.SearchMessages.INITIALIZE:
      return Object.assign({}, state, {
        topic: action.topic,
        messages: action.messages,
        next_url: action.next_url,
        search_conditions: action.search_conditions,
        search_total_count: action.search_total_count,
        fetching: false
      })
    case types.SearchMessages.FETCH_MORE_MESSAGES:
      return Object.assign({}, state, {
        messages: [...state.messages, ...action.messages],
        next_url: action.next_url,
        fetching: false
      })
    case types.SET_UA_INFO:
      return Object.assign({}, state, {
        is_mobile_app: action.is_mobile_app
      })
    case types.INIT_LAYOUT:
      return Object.assign({}, state, {
        mobile_app_layout: action.mobile_app_layout
      })

    default:
      return state;
  }
}
