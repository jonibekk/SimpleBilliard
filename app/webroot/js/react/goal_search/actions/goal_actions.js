import * as ActionTypes from "~/goal_search/constants/ActionTypes";

import {post} from "~/util/api";
import axios from "axios";
import querystring from "querystring";

export function updateInputData(data, key) {
  return {
    type: ActionTypes.UPDATE_INPUT_DATA,
    data,
    key
  }
}
export function updateData(data, key) {
  return {
    type: ActionTypes.UPDATE_DATA,
    data,
    key
  }
}
export function update(data) {
  return {
    type: ActionTypes.UPDATE_DATA,
    data,
  }
}

export function fetchInitialData() {
  return (dispatch) => {
    return axios.get(`/api/v1/goals/init_search`)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: ActionTypes.FETCH_INITIAL_DATA,
          data
        })
      })
      .catch((response) => {
      })
  }
}

export function updateFilter(data) {
  return (dispatch, getState) => {
    // 更新した検索条件によってゴール検索を実行
    let search_conditions = Object.assign(
      getState().goal_search.search_conditions,
      data
    )
    return axios.get(`/api/v1/goals/search?${querystring.stringify(search_conditions)}`)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.SEARCH,
          search_conditions,
          search_result
        })
      })
      .catch((response) => {
      })
  }
}

export function fetchMoreGoals(url) {
  return (dispatch, getState) => {
    return axios.get(`${url}`)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.FETCH_MORE_GOALS,
          search_result
        })
      })
      .catch((response) => {
      })
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
