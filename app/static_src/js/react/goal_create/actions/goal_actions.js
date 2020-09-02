import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {KeyResult} from "~/common/constants/Model";
import {post} from "../../util/api";
import axios from "axios";

export function validateGoal(page, addData) {
  return (dispatch, getState) => {

    const postData = Object.assign(getState().goal.inputData, addData)
    const fields = Page.VALIDATION_FIELDS[page].join(',')
    return post(`/api/v1/goals/validate?fields=${fields}`, postData, null,
      (response) => {
        /* eslint-disable no-console */
        console.log("validate success");
        /* eslint-enable no-console */
        dispatch(toNextPage())
      },
      ({response}) => {
        /* eslint-disable no-console */
        console.log("validate failed");
        /* eslint-enable no-console */

        // when team is in read only
        if (!response.data.validation_errors) {
          // Reason to set to validation_errors.name is that
          // This field is on to submit button
          dispatch(invalid({
            validation_errors: {name: response.data.message}
          }))
        } else {
          dispatch(invalid(response.data))
        }
      }
    );
  }
}

export function toNextPage() {
  return {
    type: types.TO_NEXT_PAGE
  }
}

export function invalid(error) {
  return {
    type: types.INVALID,
    error
  }
}

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
    dispatch(updateSuggestions(keyword, getState().goal.suggestionsExcludeSelected))
  }
}
export function onSuggestionsClearRequested() {
  return {
    type: types.CLEAR_SUGGEST
  }
}
export function onSuggestionSelected(suggestion) {
  return {
    type: types.SELECT_SUGGEST,
    suggestion,
  }
}

export function addLabel(label) {
  return {
    type: types.ADD_LABEL,
    label
  }
}
export function deleteLabel(label) {
  return {
    type: types.DELETE_LABEL,
    label
  }
}

export function updateInputData(data, key) {
  return {
    type: types.UPDATE_INPUT_DATA,
    data,
    key
  }
}

export function fetchInitialData(page) {
  const dataTypes = Page.INITIAL_DATA_TYPES[page]
  return (dispatch) => {
    return axios.get(`/api/v1/goals/init_form?data_types=${dataTypes}`)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data,
          page
        })
      })
      .catch((response) => {
      })
  }
}

export function saveGoal() {
  return (dispatch, getState) => {
    dispatch(disableSubmit())
    const postData = Object.assign({}, getState().goal.inputData)
    if (postData.key_result.value_unit == KeyResult.ValueUnit.NONE) {
      postData.key_result.start_value = 0
      postData.key_result.target_value = 1
    }
    postData.groups = Object.keys(postData.groups)

    return post("/api/v1/goals", postData, null,
      (response) => {
        // 成功時はリダイレクト
        dispatch({type: types.REDIRECT_TO_HOME})
      },
      ({response}) => {
        dispatch(invalid(response.data))
      }
    );
  }
}

export function disableSubmit() {
  return {type: types.DISABLE_SUBMIT}
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
