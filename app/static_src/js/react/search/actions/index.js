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

export function fetchInitialData() {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.LOADING,
    })
    //ゴール検索ページでセットされたクエリパラメータをゴール検索初期化APIにそのままセット
    return axios.get(`/api/v1/posts/search` + location.search)
      .then((response) => {
        let data = response.data
        const search_params = new URLSearchParams(location.search);
        let search_conditions = {
          type: search_params.get('type'),
          keyword: search_params.get('keyword'),
        };

        dispatch({
          type: ActionTypes.FETCH_INITIAL_DATA,
          data,
          search_conditions
        })
      })
      .catch((response) => {
      })
  }
}

function isfetchByOldSearchAPI(type) {
  return type === "users" || type === "circles"
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

function fetchByOldSearchAPI(type, keyword) {
  return axios.get(`/searchs/ajax_get_search_results?` + serialize({
    term: keyword,
    page_limit: 20,
    _: (new Date()).getTime()
  }), { headers: {'x-requested-with': 'XMLHttpRequest'} }).then((response) => {
    return new Promise(function(resolve, reject) {
      if (undefined === response.data["results_" + type]) {
        resolve({
          count: 0,
          data: [],
          paging: ""
        })
        return
      }
      const results = response.data["results_" + type].results;
      const data = results.map(function(v) {
        return {
          type: type,
          id: v.id.split('_')[1],// string is like "goal_11"
          text: v.text,
          image: v.image
        }
      });
      resolve({
        count: results.length,
        data: data,
        paging: ""
      })
    })
  })
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
    if (isfetchByOldSearchAPI(type)) {
      fetchByOldSearchAPI(type, getState().search.search_conditions.keyword)
        .then((data) => {
          dispatch({
            type: ActionTypes.SEARCH,
            search_conditions: search_conditions,
            search_result: data
          })
        })
        .catch((response) => {
        })
    } else {
      return axios.get(`/api/v1/posts/search?${queries}`)
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
}

export function fetchMoreResults(cursor) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING_MORE,
    })
    return axios.get(`/api/v1/posts/search?cursor=${cursor}`)
      .then((response) => {
        const search_result = response.data
        dispatch({
          type: ActionTypes.FETCH_MORE_RESULTS,
          search_result
        })
      })
      .catch((response) => {
      })
  }
}