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
  inputData:{
    key_result: {}
  }
}

export default function goal(state = initialState, action) {
  /* eslint-disable no-console */
  console.log("---reducer start---")
  console.log(action.type)
  console.log({state})
  console.log({action})
  /* eslint-disable no-console */
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
      inputData = initInputData(inputData, action.page, action.data)
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
      inputData.labels = updateSelectedLabels(inputData, action.label, true)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected : addItemToSuggestions(state.suggestionsExcludeSelected, action.label, state.labels),
      })

    case types.ADD_LABEL:
      // 追加済みラベルに新たにラベルを追加
      inputData.labels = updateSelectedLabels(inputData, action.label)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected: deleteItemFromSuggestions(state.suggestionsExcludeSelected, action.label),
        keyword:""
      })

    case types.SELECT_SUGGEST:
      // 追加済みラベルに新たにラベルを追加
      inputData.labels = updateSelectedLabels(inputData, action.suggestion.name)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected: deleteItemFromSuggestions(state.suggestionsExcludeSelected, action.suggestion.name),
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

/**
 * 選択済みラベルリストを更新
 * @param inputData
 * @param label
 * @param deleteFlg
 * @returns {*|Array}
 */
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

/**
 * 選択済みラベルを除外したサジェストリストを更新(削除用)
 * @param suggestions
 * @param suggestionName
 * @returns {*}
 */
export function deleteItemFromSuggestions(suggestions, suggestionName) {
  for (const i in suggestions) {
    if (suggestions[i].name == suggestionName) {
      suggestions.splice(i, 1)
      break;
    }
  }
  return suggestions
}

/**
 * 選択済みラベルを除外したサジェストリストを更新(追加用)
 * @param suggestions
 * @param suggestionName
 * @param baseList
 * @returns {*}
 */
export function addItemToSuggestions(suggestions, suggestionName, baseList) {
  for (const i in baseList) {
    if (baseList[i].name == suggestionName) {
      suggestions.push(baseList[i])
      break;
    }
  }
  return suggestions
}


/**
 * 画面初期化に伴う入力値初期化
 * 既に行っている場合は不要
 * @param inputData
 * @param page
 * @param data
 * @returns {{}}
 */
export function initInputData(inputData, page, data) {
  switch (page) {
    case Page.STEP2:
      if (!inputData.goal_category_id && data.categories.length > 0) {
        inputData["goal_category_id"] = data.categories[0].id
      }
      break;
    case Page.STEP3:
      if (!inputData.term_type && data.terms.length > 0) {
        inputData["term_type"] = data.terms[0].type
      }
      if (data.priorities.length > 0) {
        inputData["priority"] = data.priorities[0].id
      }
      break;
    case Page.STEP4:
      if (!inputData.key_result && data.units.length > 0) {
        inputData["key_result"] = inputData["key_result"] || {};
        inputData["key_result"]["value_unit"] = data.units[0].id
      }
      break;
    default:
      return inputData;
  }
  return inputData;
}
