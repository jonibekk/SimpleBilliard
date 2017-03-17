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
  is_fetched_initial: false,
  is_saving:false,
  err_msg:""
  // TODO:アップロードしたファイルID追加
}

export default function detail(state = initialState, action) {
  let messages = {};
  switch (action.type) {
    case ActionTypes.LOADING:
      return Object.assign({}, state, {
        loading: true
      })
    case ActionTypes.FETCH_INITIAL_DATA:
      return Object.assign({}, state, action.data, {
        loading: false,
        is_fetched_initial: true
      })
    case ActionTypes.FETCH_MORE_MESSAGES:
      messages = {
        data: [...state.messages.data, ...action.messages.data],
        paging: action.messages.paging,
      }
      return Object.assign({}, state, {
        messages,
        loading_more: false
      })
    case ActionTypes.SAVING:
      return Object.assign({}, state, {
        is_saving: true
      })
    case ActionTypes.SAVE_SUCCESS:
      messages = {
        data: [...state.messages.data, action.data],
        paging: state.messages.paging,
      }
      return Object.assign({}, state, {
        messages,
        is_saving: false
      })
    case ActionTypes.SAVE_ERROR:
      return Object.assign({}, state, {
        err_msg: action.error.message,
        is_saving: false
      })
    default:
      return state;
  }
}
