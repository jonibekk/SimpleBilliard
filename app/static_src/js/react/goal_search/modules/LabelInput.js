import * as types from "~/common/constants/action_types/LabelInput";
import * as goal_actions from "~/goal_search/actions/goal_actions";


export * from "~/common/constants/action_types/LabelInput"

export function setKeyword(keyword) {
  // 最初にフォーカスしてサジェストのリストを全出しした後に↓キーを押すと
  // なぜか文字列ではなく関数が渡されてくるので文字列チェック
  keyword = typeof(keyword) == "string" ? keyword : "";
  return {
    type: types.SET_KEYWORD,
    keyword
  }
}

export function updateSuggestions(keyword, suggestions) {
  return {
    type: types.REQUEST_SUGGEST,
    suggestions: getSuggestions(keyword, suggestions),
    keyword
  }
}
export function onSuggestionsFetchRequested(keyword) {
  return (dispatch, getState) => {
    dispatch(updateSuggestions(keyword, getState().goal_search.suggestions_exclude_selected))
  }
}
export function onSuggestionsClearRequested() {
  return {
    type: types.CLEAR_SUGGEST
  }
}
export function onSuggestionSelected(suggestion) {
  return (dispatch, getState) => {
    // 追加したラベルリストによって検索
    let labels = getState().goal_search.search_conditions.labels

    labels = updateSelectedLabels(labels, suggestion.name)
    dispatch(goal_actions.updateFilter({labels}))
    dispatch(onSuggestionSelectedForDispatch(suggestion, labels))
  }
}

export function onSuggestionSelectedForDispatch(suggestion, labels) {

  return {
    type: types.SELECT_SUGGEST,
    suggestion,
    labels
  }
}

export function addLabel(label) {
  return (dispatch, getState) => {
    // 追加したラベルリストによって検索
    let labels = getState().goal_search.search_conditions.labels
    labels = updateSelectedLabels(labels, label)
    dispatch(goal_actions.updateFilter({labels}))
    dispatch(addLabelForDispatch(label, labels))
  }
}

export function addLabelForDispatch(label, labels) {
  return {
    type: types.ADD_LABEL,
    label,
    labels
  }
}

export function deleteLabel(label) {
  return (dispatch, getState) => {
    // 追加済みラベルから対象のラベルを削除
    let labels = getState().goal_search.search_conditions.labels
    labels = updateSelectedLabels(labels, label, true)
    // 追加したラベルリストによって検索
    dispatch(goal_actions.updateFilter({labels}))
    dispatch(deleteLabelForDispatch(label, labels))


  }
}

export function deleteLabelForDispatch(label, labels) {
  return {
    type: types.DELETE_LABEL,
    label,
    labels
  }
}

/**
 * 入力値にマッチしたサジェストのリストを取得
 * 空文字(フォーカス時等でもサジェスト表示許可
 *
 * @param value
 * @param suggestions
 * @returns {*}
 */
function getSuggestions(value, suggestions) {
  if (value) {
    value = value.trim();
    const regex = new RegExp('^' + value, 'i');
    suggestions = suggestions.filter((suggestion) => regex.test(suggestion.name));
  }

  // サジェストは10件のみ表示
  suggestions = suggestions.slice(0, 10)
  // サジェストをラベル名昇順に並び替え
  suggestions.sort((a, b) => {
    return (a.goal_label_count < b.goal_label_count) ? 1 : -1
  });

  // サジェストの先頭に入力文字列を加える
  if (value) {
    suggestions.unshift({name: value})
  }
  return suggestions
}


/* アクション */
/**
 * 選択済みラベルリストを更新
 * @param labels
 * @param label
 * @param delete_flg
 * @returns {*|Array}
 */
export function updateSelectedLabels(labels, label, delete_flg = false) {
  labels = Array.isArray(labels) ? labels : [];

  if (!label) {
    return labels
  }
  const idx = labels.indexOf(label)
  if (delete_flg && idx != -1) {
    labels.splice(idx, 1)
  } else if (!delete_flg && idx == -1) {
    labels.push(label)
  }

  return labels

}

/**
 * 選択済みラベルを除外したサジェストリストを更新(削除用)
 * @param suggestions
 * @param suggestion_name
 * @returns {*}
 */
export function deleteItemFromSuggestions(suggestions, suggestion_name) {
  for (let i = 0; i < suggestions.length; i++) {
    if (suggestions[i].name == suggestion_name) {
      suggestions.splice(i, 1)
      break;
    }
  }

  return suggestions
}

/**
 * 選択済みラベルを除外したサジェストリストを更新(追加用)
 * @param suggestions
 * @param suggestion_name
 * @param base_list
 * @returns {*}
 */
export function addItemToSuggestions(suggestions, suggestion_name, base_list) {
  for (let i = 0; i < base_list.length; i++) {
    if (base_list[i].name == suggestion_name) {
      suggestions.push(base_list[i])
      break;
    }
  }
  return suggestions
}
