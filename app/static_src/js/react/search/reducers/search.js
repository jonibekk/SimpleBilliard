import * as ActionTypes from "~/search/constants/ActionTypes";

const initialState = {
  categories: [],
  labels: [],
  search_result: {
    data: [],
    count: 0,
    paging: {
      next: ''
    }
  },
  keyword: "",
  search_conditions: {
    type: "post"
  },
  loading: false,
  loading_more: false
}

export default function search(state = initialState, action) {
  let search_conditions = state.search_conditions
  let search_result = state.search_result

  switch (action.type) {
    case ActionTypes.FETCH_INITIAL_DATA:
      return Object.assign({}, state, action.data, {
        loading: false
      })

    case ActionTypes.UPDATE_DATA:
      if (action.key) {
        let data = state[key]
        // 多次元配列のマージの場合Object.assignでバグが発生するので以下のように処理
        // TODO:配列マージ用の共通関数を作成
        data[action.key] = data[action.key] || {}
        data[action.key] = Object.assign({}, data[action.key], action.data)
        state[key] = data
        return Object.assign({}, state)
      }
      return Object.assign({}, state, action.data)

    case ActionTypes.UPDATE_KEYWORD:
      search_conditions.keyword = action.data.keyword
      return Object.assign({}, state, {
        search_conditions,
      })

    case ActionTypes.SEARCH:
      return Object.assign({}, state, {
        search_result: action.search_result,
        search_conditions: action.search_conditions,
        loading: false
      })

    case ActionTypes.FETCH_MORE_RESULTS:
      search_result = {
        data: [...state.search_result.data, ...action.search_result.data],
        paging: action.search_result.paging,
        count: action.search_result.count
      }
      return Object.assign({}, state, {
        search_result,
        loading_more: false
      })

    case ActionTypes.LOADING:
      return Object.assign({}, state, {
        loading: true
      })

    case ActionTypes.LOADING_MORE:
      return Object.assign({}, state, {
        loading_more: true
      })

    default:
      return state;
  }
}
