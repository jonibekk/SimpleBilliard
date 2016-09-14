import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {post} from "./common_actions";
import axios from "axios";

export function validateGoal(page) {
  return (dispatch, getState) => {
    let postData = Object.assign(getState().goal.inputData, {
      fields: Page.VALIDATION_FIELDS[page].join(',')
    })
    console.log({postData})
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

export function fetchInitialData(dispatch) {
  return (dispatch) => {
    return axios.get('/api/v1/goals/init_form?data_types=categories,labels')
      .then((response) => {
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data: response.data.data,
        })
      })
      .catch((response) => {
      })
  }
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
