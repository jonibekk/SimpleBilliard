import * as ActionTypes from "~/goal_search/constants/ActionTypes";
import {post, del} from "~/util/api";
import axios from "axios";
import querystring from "querystring";
import { saveAs } from 'file-saver';

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
    return axios.get(`/api/v1/goals/init_search` + location.search)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: ActionTypes.FETCH_INITIAL_DATA,
          data,
        })
      })
      .catch((response) => {
      })
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
      getState().goal_search.search_conditions,
      data
    )

    let queries = Object.assign({}, search_conditions)
    //querystring.stringifyすると配列がqueryのkeyダブってセットされてしまう(hoge=aaa&hoge=bbb)ので、hoge[]にキーを事前に書き換える
    if ('labels' in queries) {
      queries["labels[]"] = queries.labels
      delete queries.labels
    }
    queries = querystring.stringify(queries)
    history.pushState(null, "", '?' + queries);

    return axios.get(`/api/v1/goals/search?${queries}`)
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

export function downloadCsv() {

  return (dispatch, getState) => {
    dispatch({type: ActionTypes.DOWNLOADING_CSV})
    // 更新した検索条件によってゴール検索を実行
    let queries = Object.assign({}, getState().goal_search.search_conditions);
    //querystring.stringifyすると配列がqueryのkeyダブってセットされてしまう(hoge=aaa&hoge=bbb)ので、hoge[]にキーを事前に書き換える
    if ('labels' in queries) {
      queries["labels[]"] = queries.labels
      delete queries.labels
    }
    queries = querystring.stringify(queries)

    return axios.get(`/api/v1/goals/download_csv?${queries}`, {responseType: 'arraybuffer'})
      .then((response) => {
        var filename = "";
        var disposition = response.headers['content-disposition'];
        if (disposition && disposition.indexOf('attachment') !== -1) {
          var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
          var matches = filenameRegex.exec(disposition);
          if (matches != null && matches[1]) {
            filename = matches[1].replace(/['"]/g, '');
          }
        }
        var blob = new Blob([response.data], {type: 'application/octet-stream'});
        saveAs(blob, filename);
      })
      .catch((response) => {
      })
      .then(function () {
        dispatch({type: ActionTypes.DOWNLOADED_CSV})
      });
  }
}

export function fetchMoreGoals(url) {
  return (dispatch, getState) => {
    dispatch({
      type: ActionTypes.LOADING_MORE,
    })
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

export function follow(goal_id) {
  return (dispatch, getState) => {
    return post(`/api/v1/goals/${goal_id}/follow`)
      .then((response) => {
        dispatch({
          type: ActionTypes.FOLLOW,
          goal_id
        })
      })
      .catch((response) => {
      })
  }
}

export function unfollow(goal_id) {
  return (dispatch, getState) => {
    return del(`/api/v1/goals/${goal_id}/follow`)
      .then((response) => {
        dispatch({
          type: ActionTypes.UNFOLLOW,
          goal_id
        })
      })
      .catch((response) => {
      })
  }
}
