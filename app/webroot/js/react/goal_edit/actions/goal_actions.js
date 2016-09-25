import * as types from "../constants/ActionTypes";
import {post} from "../../util/api";
import axios from "axios";

export function validateGoal(page, addData) {
  return (dispatch, getState) => {

    const postData = Object.assign(getState().goal.inputData, addData)
    return post(`/api/v1/goals/validate`, postData, null,
      (response) => {
        console.log("validate success");
        dispatch(toNextPage())
      },
      (response) => {
        console.log("validate failed");
        dispatch(invalid(response.data))
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
    error: error
  }
}

export function setKeyword(keyword) {
  // 最初にフォーカスしてサジェストのリストを全出しした後に↓キーを押すと
  // なぜか文字列ではなく関数が渡されてくるので文字列チェック
  keyword = typeof(keyword) == "string" ? keyword : "";
  return {
    type: types.SET_KEYWORD,
    keyword: keyword
  }
}

export function updateSuggestions(keyword, suggestions) {
  return {
    type: types.REQUEST_SUGGEST,
    suggestions: getSuggestions(keyword, suggestions),
    keyword: keyword
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

export function fetchInitialData(goalId) {
  return (dispatch) => {
    return axios.get(`/api/v1/goals/${goalId}/init_form?data_types=all`)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data
        })
      })
      .catch((response) => {
      })
  }
}

export function fetchComments() {
  return (dispatch, getState) => {
    const collaboratorId = getState().goal.goal.collaborator.id
    return axios.get(`/api/v1/goal_approvals/histories?collaborator_id=${collaboratorId}`)
      .then((response) => {
        let approvalHistories = response.data.data
        dispatch({
          type: types.FETCH_COMMETNS,
          approvalHistories
        })
      })
      .catch((response) => {
      })
  }
}

export function saveGoal(addData) {
  return (dispatch, getState) => {
    const {inputData, goal} = getState().goal;
    inputData["approval_history"] = addData
    return post(`/api/v1/goals/${goal.id}/update`, inputData, null,
      (response) => {
        dispatch(toNextPage())
      },
      (response) => {
        dispatch(invalid(response.data))
      }
    );
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
  if (!value) {
    return suggestions.filter((suggestion) => suggestion.name);
  }
  value = value.trim();
  const regex = new RegExp('^' + value, 'i');
  return suggestions.filter((suggestion) => regex.test(suggestion.name));
}

