import * as ActionTypes from "~/goal_search/constants/ActionTypes";
import * as LabelInput from "~/goal_search/modules/LabelInput";

const initialState = {
  categories: [],
  labels: [],
  search_result:{
    data:[],
    count:0,
    paging: {
      next:''
    }
  },
  keyword: "",
  label_keyword: "",
  suggestions: [],
  suggestions_exclude_selected: [],
  search_conditions: {},
  showFilter:false
}

export default function goal_search(state = initialState, action) {
  let search_conditions = Object.assign({}, state.search_conditions)

  switch (action.type) {
    case ActionTypes.FETCH_INITIAL_DATA:
      return Object.assign({}, state, action.data, {
        suggestions_exclude_selected:[...action.data.labels],
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

    case ActionTypes.SEARCH:
      return Object.assign({}, state, {
        search_result: action.search_result,
        search_conditions: action.search_conditions,
      })


    /* LabelInput */
    case LabelInput.REQUEST_SUGGEST:
      return Object.assign({}, state, {
        suggestions: action.suggestions,
        label_keyword: action.keyword
      })
    case LabelInput.CLEAR_SUGGEST:
      return Object.assign({}, state, {
        label_keyword:""
      })

    case LabelInput.SET_KEYWORD:
      return Object.assign({}, state, {
        label_keyword: action.keyword
      })

    case LabelInput.DELETE_LABEL:
      // 追加済みラベルから対象のラベルを削除
      // search_conditions.labels = LabelInput.updateSelectedLabels(search_conditions, action.label, true)
      search_conditions.labels = [...action.labels]
      return Object.assign({}, state, {
        search_conditions,
        suggestions_exclude_selected: LabelInput.addItemToSuggestions(state.suggestions_exclude_selected, action.label, [...state.labels]),
      })


    case LabelInput.ADD_LABEL:
      // 追加済みラベルに新たにラベルを追加
      // search_conditions.labels = LabelInput.updateSelectedLabels(search_conditions, action.label)
      search_conditions.labels = [...action.labels]

      return Object.assign({}, state, {
        search_conditions,
        suggestions_exclude_selected: LabelInput.deleteItemFromSuggestions(state.suggestions_exclude_selected, action.label),
        label_keyword: ""
      })

    case LabelInput.SELECT_SUGGEST:
      // 追加済みラベルに新たにラベルを追加
      // search_conditions.labels = LabelInput.updateSelectedLabels(search_conditions, action.suggestion.name)
      search_conditions.labels = [...action.labels]


      return Object.assign({}, state, {
        search_conditions,
        suggestions_exclude_selected: LabelInput.deleteItemFromSuggestions(state.suggestions_exclude_selected, action.suggestion.name),
        label_keyword: ""
      })

    default:
      return state;
  }
}
