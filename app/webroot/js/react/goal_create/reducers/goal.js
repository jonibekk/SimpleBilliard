import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";

const initialState = {
  toNextPage: false,
  categories:[],
  labels:[],
  terms:[],
  priorities:[],
  units:[],
  keyword: "",
  suggestions: [],
  validationErrors: {
    key_result: {}
  },
  inputData:{}
}

export default function goal(state = initialState, action) {
  let inputData = state.inputData
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, {
        validationErrors: action.error.validation_errors
      })
    case types.TO_NEXT_PAGE:
      return Object.assign({}, state, {
        toNextPage: true
      })
    case types.FETCH_INITIAL_DATA:
      let newState = Object.assign({}, state, {toNextPage: false})
      inputData = Object.assign({}, inputData, action.initInputData)
      return Object.assign({}, newState, action.data, {inputData})
    case types.REQUEST_SUGGEST:
      return Object.assign({}, state, {
        suggestions: action.suggestions,
        keyword: action.keyword
      })
    case types.CLEAR_SUGGEST:
      return Object.assign({}, state)
    case types.SET_KEYWORD:
      return Object.assign({}, state, {
        keyword: action.keyword
      })
    case types.SELECT_SUGGEST:
      inputData.labels = inputData.labels || [];
      inputData.labels.push(action.suggestion.name)
      return Object.assign({}, state, {
        inputData,
        keyword:""
      })
    case types.UPDATE_INPUT_DATA:
      if (action.key) {
        // 多次元配列のマージの場合Object.assignでバグが発生するので以下のように処理
        // TODO:配列マージ用の共通関数を作成
        inputData[action.key] = inputData[action.key] || {}
        inputData[action.key] = Object.assign({}, inputData[action.key], action.data)
        state.inputData = inputData
        return Object.assign({}, state)
      } {
        inputData = Object.assign({}, inputData, action.data)
        return Object.assign({}, state, {
          inputData
        })
      }
    default:
      return state;
  }
}
