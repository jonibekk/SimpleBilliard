import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {post} from "./common_actions";
import axios from "axios";

export function validateGoal(page, addData) {
  return (dispatch, getState) => {
    let postData = Object.assign(getState().goal.inputData, addData, {
      fields: Page.VALIDATION_FIELDS[page].join(',')
    })
    return post('/api/v1/goals/validate', postData, null,
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
    const labels = getState().goal.labels;
    dispatch(updateSuggestions(keyword, labels))
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
    suggestion: suggestion
  }
}
export function updateInputData(data) {
  return {
    type: types.UPDATE_INPUT_DATA,
    data: data
  }
}

export function fetchInitialData(page) {
  const dataTypes = Page.INITIAL_DATA_TYPES[page]
  return (dispatch, getState) => {
    return axios.get(`/api/v1/goals/init_form?data_types=${dataTypes}`)
      .then((response) => {
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data: response.data.data,
          initInputData:initInputData(page, response.data.data),
          page
        })
      })
      .catch((response) => {
      })
  }
}

/**
 * 画面初期化に伴う入力値初期化
 * @param page
 * @param data
 * @returns {{}}
 */
function initInputData(page, data) {
  console.log("initInputData start")
  let inputData = {}
  switch(page) {
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
        inputData.priority = data.priorities[0]
      }
      break;
    default:
      return inputData;
  }
  return inputData;
}


/**
 * 入力値にマッチしたサジェストのリストを取得
 *
 * @param value
 * @param suggestions
 * @returns {*}
 */
function getSuggestions(value, suggestions) {
  value = value.trim();
  if (value === '') {
    return [];
  }
  const regex = new RegExp('^' + value, 'i');
  return suggestions.filter((suggestion) => regex.test(suggestion.name));
}
