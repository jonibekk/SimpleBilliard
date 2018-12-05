import * as ActionTypes from "~/search/constants/ActionTypes";
import {post, del} from "~/util/api";
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

function serialize(obj, prefix) {
  var str = [],
    p;
  for (p in obj) {
    if (obj.hasOwnProperty(p)) {
      var k = prefix ? prefix + "[" + p + "]" : p,
        v = obj[p];
      str.push((v !== null && typeof v === "object") ?
        serialize(v, k) :
        encodeURIComponent(k) + "=" + encodeURIComponent(v));
    }
  }
  return str.join("&");
}

function createFetchInitial(type, keyword) {
  const limit = 5;
  switch (type) {
    case "circle_post":
    case "action":
      return axios.get(`/api/v1/posts/search?` + serialize({
        keyword: keyword,
        type: type,
        limit: limit
      }));
    case "users":
      return axios.get(`/api/v1/users/search?` + serialize({
        keyword: keyword,
        limit: limit
      }));
    case "circles":
      return axios.get(`/api/v1/circles/search?` + serialize({
        keyword: keyword,
        limit: limit
      }));
  }
}

function createFetchMore(type, cursor) {
  switch (type) {
    case "circle_post":
    case "action":
      return axios.get(`/api/v1/posts/search?cursor=` + cursor);
    case "users":
      return axios.get(`/api/v1/users/search?cursor=` + cursor);
    case "circles":
      return axios.get(`/api/v1/circles/search?cursor=` + cursor);
  }
}

export function updateKeyword(data) {
  return {
    type: ActionTypes.UPDATE_KEYWORD,
    data,
  }
}

export function updateFilter(data) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING,
    })

    // 更新した検索条件によってゴール検索を実行
    let search_conditions = Object.assign(
      getState().search.search_conditions,
      data
    )

    let queries = Object.assign({}, search_conditions)
    queries = querystring.stringify(queries)
    history.pushState(null, "", '?' + queries);

    const type = getState().search.search_conditions.type
    const keyword = getState().search.search_conditions.keyword
    return createFetchInitial(type, keyword)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.SEARCH,
          search_conditions,
          search_result
        })
      })
      .catch((response) => {
        dispatch({
          type: ActionTypes.SEARCH,
          search_conditions: search_conditions,
          search_result: {
            data: [],
            count: 0,
            paging: ""
          }
        })
      })
  }
}

export function fetchMoreResults(param) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING_MORE,
    })
    return createFetchMore(param.type, param.cursor)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.FETCH_MORE_RESULTS,
          search_result
        })
      })
      .catch((response) => {
        dispatch({
          type: ActionTypes.FETCH_MORE_RESULTS,
          search_result: {
            data: [],
            paging: ""
          }
        })
      })
  }
}