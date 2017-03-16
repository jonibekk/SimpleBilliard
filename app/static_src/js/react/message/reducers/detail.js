import * as ActionTypes from '~/message/constants/ActionTypes'

const initialState = {
  topic: {},
  messages: {
    data: [],
    paging: {
      next: ""
    }
  },
  loading: false,
  loading_more: false,
  is_fetched_initial: false
  // TODO:アップロードしたファイルID追加
}

export default function detail(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.LOADING:
      return Object.assign({}, state, {
        loading: true
      })
    case ActionTypes.FETCH_INITIAL_DATA:
  console.log(action.data);
      return Object.assign({}, state, action.data, {
        loading: false,
        is_fetched_initial: true
      })
    case ActionTypes.FETCH_MORE_MESSAGES:
      const messages = {
        data: [...state.messages.data, ...action.messages.data],
        paging: action.messages.paging,
      }
      return Object.assign({}, state, {
        messages,
        loading_more: false
      })
    default:
      return state;
  }
}
