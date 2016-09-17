import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {post} from "./common_actions";
import axios from "axios";

export function validateGoal(page, addData) {
  return (dispatch, getState) => {

    const postData = Object.assign(getState().goal.inputData, addData)
    const fields = Page.VALIDATION_FIELDS[page].join(',')
    return post(`/api/v1/goals/validate?fields=${fields}`, postData, null,
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
        if (page == Page.STEP2) {
          data["suggestionsExcludeSelected"] = Object.assign([], data.labels)
        }
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data,
          initInputData: initInputData(page, response.data.data),
          page
        })
      })
      .catch((response) => {
      })
  }
}

export function saveGoal() {
  return (dispatch, getState) => {
    return post("/api/v1/goals", getState().goal.inputData, null,
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
 * 画面初期化に伴う入力値初期化
 * @param page
 * @param data
 * @returns {{}}
 */
function initInputData(page, data) {
  let inputData = {}
  switch (page) {
    case Page.STEP2:
      if (data.categories.length > 0) {
        inputData["goal_category_id"] = data.categories[0].id
      }
      break;
    case Page.STEP3:
      if (data.terms.length > 0) {
        inputData["term_type"] = data.terms[0].type
      }
      if (data.priorities.length > 0) {
        inputData["priority"] = data.priorities[0].id
      }
      break;
    case Page.STEP4:
      if (data.units.length > 0) {
        inputData["key_result"] = inputData["key_result"] || {};
        inputData["key_result"]["value_unit"] = data.units[0].id
      }
      break;
    default:
      return inputData;
  }
  return inputData;
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
  value = value.trim();
  const regex = new RegExp('^' + value, 'i');
  return suggestions.filter((suggestion) => regex.test(suggestion.name));
}

