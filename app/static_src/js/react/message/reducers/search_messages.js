import * as types from '~/message/constants/ActionTypes'

const initialState = {
  topic: {},
  messages: [],
  next_url: '',
  fetching: false,
  is_mobile_app: false,
  mobile_app_layout: {
    header_top: null,
    body_top: null,
    body_bottom: null,
    footer_bottom: null,
  }

}

export default function search_messages(state = initialState, action) {
  switch (action.type) {
    case types.FETCHING:
      return Object.assign({}, state, {
        fetching: true
      })
    case types.INITIALIZE:
      return Object.assign({}, state, {
        topic: action.data.topic,
        messages: action.data.messages,
        next_url: action.data.next_url,
        fetching: false
      })
    case types.FETCH_MORE_MESSAGES:
      return Object.assign({}, state, {
        messages: [...state.messages, ...action.data.messages],
        next_url: action.data.next_url,
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
