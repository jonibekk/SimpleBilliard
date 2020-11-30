import * as types from "../constants/ActionTypes";

const initialState = {
  initFlg: false,
  toNextPage: false,
  goal: {},
  visions: [],
  categories: [],
  labels: [],
  terms: [],
  priorities: [],
  units: [],
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
      current_value:"",
      description: ""
    },
    labels: [],
    groups: {}
  },
  approvalHistories: [],
  isDisabledSubmit: false,
  from: "",
  groups_enabled: false,
  show_approve: false,
  coach_present: false
}

export default function goal(state = initialState, action) {
  let inputData = state.inputData
  switch (action.type) {
    case types.INIT:
      return Object.assign({}, state, action.data)

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
        toNextPage: true
      })

    case types.FETCH_INITIAL_DATA:
      let suggestionsExcludeSelected = state.suggestionsExcludeSelected
      const existLabelIds = action.data.goal.goal_labels.map((el) => {
        return el.id
      })
      if (state.suggestionsExcludeSelected.length == 0) {
        suggestionsExcludeSelected = action.data.labels.filter((el) => {
          return (existLabelIds.indexOf(el.id) == -1);
        })
      }
      if (!state.initFlg) {
        inputData = initInputData(action.data.goal)
      }
      return Object.assign({}, state, action.data, {
        inputData,
        suggestionsExcludeSelected,
        toNextPage: false,
        initFlg: true,
        validationErrors: {key_result: {}}
      })

    case types.FETCH_COMMETNS:
      return Object.assign({}, state, {
        approvalHistories: action.approvalHistories,
        toNextPage: false,
        initFlg: true,
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

      inputData = Object.assign({}, inputData, action.data)
      return Object.assign({}, state, {
        inputData
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
 * @param data
 * @returns {{}}
 */
export function initInputData(goal) {
  const labels = [];

  for (const i in goal.goal_labels) {
    labels.push(goal.goal_labels[i].name)
  }

  const inputData = {
    name: goal.name,
    goal_category_id: goal.goal_category_id,
    labels,
    end_date: goal.end_date,
    description: goal.description,
    priority: goal.goal_member.priority,
    term_type: goal.term_type,
    key_result: {
      name: goal.top_key_result.name,
      value_unit: goal.top_key_result.value_unit,
      start_value: goal.top_key_result.start_value,
      target_value: goal.top_key_result.target_value,
      current_value: goal.top_key_result.current_value,
      description: goal.top_key_result.description
    },
    groups: {}
  }
  return inputData;
}
