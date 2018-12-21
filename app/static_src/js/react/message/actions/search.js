import * as types from "~/message/constants/ActionTypes"
import { get } from "~/util/api"
import querystring from "querystring";
import axios from "axios";

export function inputKeyword(keyword) {
  return (dispatch) => {
    dispatch({
      type: types.INPUT_KEYWORD,
      keyword
    })
    const trimmed_keyword = keyword.trim()
    if(trimmed_keyword == '') {
      dispatch({
        type: types.INITIALIZE_SEARCH
      })
    } else {
      dispatch(search({keyword:trimmed_keyword}))
    }
  }
}

export function emptyTopics() {
  return (dispatch) => {
    dispatch({
      type: types.INITIALIZE_SEARCH
    })
  }
}

export function fetchMoreSearch(url) {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING_SEARCH
    })
    return get(url)
      .then((response) => {
        const topics = response.data.data
        const next_url = response.data.paging.next
        dispatch({
          type: types.FETCH_MORE_SEARCH,
          data: {
            topics,
            next_url
          }
        })
      })
      .catch((response) => {
        /* eslint-disable no-console */
        console.log(response)
        /* eslint-enable no-console */
      })
  }
}

export function changeSearchType(type) {
  return (dispatch) => {
    dispatch(search({type}));
  }
}

/**
 * search topics by keyword
 * - search request by keyword 0.5 sec later since user input keyword
 * - if user input other word within 0.5 sec, not sending request by previous word.
 *
 * @param  string keyword
 * @param  string search_type
 */
export function search(data) {
  return (dispatch, getState) => {
    let search_conditions = Object.assign(
      getState().search.search_conditions,
      data
    )
    const queries = Object.assign({}, search_conditions);
    const qs = '?' + querystring.stringify(queries);
    history.pushState(null, "", qs);

    dispatch({
      type: types.UPDATE_SEARCH_CONDITION,
      search_conditions,
    })
    return setTimeout(() => {
      if (getState().search.search_conditions.keyword != search_conditions.keyword) {
        return;
      }
      // TODO: separate calling api by type
      return get(`/api/v1/topics/search${qs}`)
        .then((response) => {
          if (getState().search.search_conditions.keyword != search_conditions.keyword) {
            return
          }

          const topics = response.data.data
          const next_url = response.data.paging.next

          dispatch({
            type: types.SEARCH,
            topics,
            next_url
          })
        })
        .catch((response) => {
          /* eslint-disable no-console */
          console.log(response)
          /* eslint-enable no-console */
        })
    }, 500)
  }
}

// export function fetchInitialData() {
//   if (location.search === '') {
//     return {
//       type: types.FETCH_INITIAL_DATA
//     };
//   }
//   return (dispatch) => {
//
//     dispatch({
//       type: types.LOADING,
//     })
//     //ゴール検索ページでセットされたクエリパラメータをゴール検索初期化APIにそのままセット
//     return axios.get(`/api/v1/goals/init_search` + location.search)
//       .then((response) => {
//         let data = response.data.data
//         dispatch({
//           type: types.FETCH_INITIAL_DATA,
//           data,
//         })
//       })
//       .catch((response) => {
//       })
//   }
// }


export function setTopicOnDetail(topic) {
  return {
    type: types.SET_TOPIC_ON_DETAIL,
    topic
  }
}
