import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {KeyResult} from "~/common/constants/Model";

const initialState = {
  toNextPage: false,
  visions: [],
  categories: [],
  labels: [],
  terms: {},
  priorities: [],
  units: [],
  groups: [],
  default_end_dates: {},
  keyword: "",
  suggestions: [],
  suggestionsExcludeSelected: [],
  validationErrors: {
    key_result: {}
  },
  inputData: {
    name: "",
    end_date: "",
    description: "",
    key_result: {
      name: "",
      start_value: 0,
      target_value: 100,
      description: ""
    },
    groups: {}
  },
  isDisabledSubmit: false,
  show_approve: false,
  coach_present: false,
  redirect_to_home: false,
  groups_enabled: false
}

export default function goal(state = initialState, action) {
  let inputData = state.inputData
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, {
        validationErrors: action.error.validation_errors,
        isDisabledSubmit: false
      })
    case types.DISABLE_SUBMIT:
      return Object.assign({}, state, {
        isDisabledSubmit: true
      })
    case types.TO_NEXT_PAGE:
      return Object.assign({}, state, {
        toNextPage: true,
        isDisabledSubmit: false
      })
    case types.FETCH_INITIAL_DATA:
      let suggestionsExcludeSelected = state.suggestionsExcludeSelected
      if (action.page == Page.STEP2 && state.suggestionsExcludeSelected.length == 0) {
        suggestionsExcludeSelected = Object.assign([], action.data.labels)
      }
      inputData = initInputData(inputData, action.page, action.data)
      return Object.assign({}, state, action.data, {
        inputData,
        suggestionsExcludeSelected,
        toNextPage: false,
        validationErrors: {key_result: {}}
      })

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

    case types.DELETE_LABEL:
      // 追加済みラベルから対象のラベルを削除
      inputData.labels = updateSelectedLabels(inputData, action.label, true)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected: addItemToSuggestions(state.suggestionsExcludeSelected, action.label, state.labels)
      })

    case types.ADD_LABEL:
      // 追加済みラベルに新たにラベルを追加
      inputData.labels = updateSelectedLabels(inputData, action.label)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected: deleteItemFromSuggestions(state.suggestionsExcludeSelected, action.label),
        keyword: ""
      })

    case types.SELECT_SUGGEST:
      // 追加済みラベルに新たにラベルを追加
      inputData.labels = updateSelectedLabels(inputData, action.suggestion.name)

      return Object.assign({}, state, {
        inputData,
        suggestionsExcludeSelected: deleteItemFromSuggestions(state.suggestionsExcludeSelected, action.suggestion.name),
        keyword: ""
      })

    case types.UPDATE_INPUT_DATA:
      if (action.key) {
        // 多次元配列のマージの場合Object.assignでバグが発生するので以下のように処理
        // TODO:配列マージ用の共通関数を作成
        inputData[action.key] = inputData[action.key] || {}
        inputData[action.key] = Object.assign({}, inputData[action.key], action.data)
        state.inputData = inputData
        return Object.assign({}, state)
      }
      {
        inputData = Object.assign({}, inputData, action.data)
        return Object.assign({}, state, {
          inputData
        })
      }
    case types.REDIRECT_TO_HOME:
      return Object.assign({}, state, {
        redirect_to_home: true
      })
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
  const labels = inputData.labels || [];
  if (!label) {
    return labels
  }
  const idx = labels.indexOf(label)
  if (deleteFlg && idx != -1) {
    labels.splice(idx, 1)
  } else if (!deleteFlg && idx == -1) {
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
      inputData["term_type"] = 'current'
      if (!inputData.priority && data.priorities.length > 0) {
        inputData["priority"] = KeyResult.Priority.DEFAULT;
      }
      if (!inputData.end_date && Object.keys(data.default_end_dates).length > 0) {
        inputData["end_date"] = data.default_end_dates.current
      }
      break;
    case Page.STEP4:
      if (Object.keys(inputData.key_result).length > 0 && inputData.key_result.value_unit) {
        break;
      }
      inputData["key_result"] = inputData.key_result || {};

      if (data.units.length > 0) {
        inputData.key_result["value_unit"] = data.units[0].id
      }
      inputData.key_result["start_value"] = 0
      inputData.key_result["target_value"] = 100

      break;
    default:
      return inputData;
  }
  return inputData;
}
