import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";

const initialState = {
  toNextPage: false,
  visions:[],
  categories:[],
  labels:[],
  terms:[],
  priorities:[],
  units:[],
  keyword: "",
  suggestions: [],
  suggestionsExcludeSelected: [],
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
      // サジェストをラベル名昇順に並び替え
      action.suggestions.sort((a,b) => {
        return (a.name > b.name)? 1 :-1
      });

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

    case types.DELETE_LABEL:
      // 追加済みラベルから対象のラベルを削除
      // inputData.labels = inputData.labels || [];
      // const idx = inputData.labels.indexOf(action.label)
      // if (idx != -1) {
      //   inputData.labels.splice(idx, 1)
      // }
      inputData.labels = updateSelectedLabels(inputData, action.label, true)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected : addSuggestion(state.suggestionsExcludeSelected, action.label, state.labels),
      })

    case types.ADD_LABEL:
      // 追加済みラベルから新たにラベルを追加
      // inputData.labels = inputData.labels || [];
      // const idx = inputData.labels.indexOf(action.label)
      // if (idx == -1) {
      //   inputData.labels.push(action.label)
      // }
      inputData.labels = updateSelectedLabels(inputData, action.label)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected: deleteSuggestion(state.suggestionsExcludeSelected, action.label),
        keyword:""
      })

    case types.SELECT_SUGGEST:
      inputData.labels = updateSelectedLabels(inputData, action.suggestion.name)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected: deleteSuggestion(state.suggestionsExcludeSelected, action.suggestion.name),
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

export function updateSelectedLabels(inputData, label, deleteFlg = false) {
  let labels = inputData.labels || [];
  if (!label) {
    return labels
  }
  const idx = labels.indexOf(label)
  if (deleteFlg && idx != -1) {
    labels.splice(idx, 1)
  } else if(!deleteFlg && idx == -1)  {
    labels.push(label)
  }

  return labels

}

export function deleteSuggestion(suggestions, suggestionName) {
  for (const i in suggestions) {
    if (suggestions[i].name == suggestionName) {
      suggestions.splice(i, 1)
      break;
    }
  }
  return suggestions
}

export function addSuggestion(suggestions, suggestionName, baseList) {
  for (const i in baseList) {
    if (baseList[i].name == suggestionName) {
      suggestions.push(baseList[i])
      break;
    }
  }
  return suggestions
}
