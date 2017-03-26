import * as types from "~/message/constants/ActionTypes";
import { get } from "~/util/api";

export function fetchInitData() {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING_TOPICS
    })
    return get('/api/v1/topics/search')
      .then((response) => {
        const topics = response.data.data
        const next_url = response.data.paging.next
        dispatch({
          type: types.FETCH_TOPICS,
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

export function fetchMoreTopics(url) {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING_TOPICS
    })
    return get(url)
      .then((response) => {
        const topics = response.data.data
        const next_url = response.data.paging.next
        dispatch({
          type: types.FETCH_MORE_TOPICS,
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

export function changeToSearchMode() {
  return (dispatch) => {
    dispatch({
      type: types.CHANGE_TO_SEARCH_MODE
    })
  }
}

export function cancelSearchMode() {
  return (dispatch) => {
    dispatch({
      type: types.CANCEL_SEARCH_MODE
    })
  }
}

export function fetchMoreSearchTopics(url) {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING_SEARCH_TOPICS
    })
    return get(url)
      .then((response) => {
        const topics_searched = response.data.data
        const next_search_url = response.data.paging.next
        dispatch({
          type: types.FETCH_MORE_SEARCH_TOPICS,
          data: {
            topics_searched,
            next_search_url
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

export function inputSearchKeyword(keyword) {
  return (dispatch) => {
    dispatch({
      type: types.INPUT_SEARCH_KEYWORD,
      keyword
    })
    const trimmed_keyword = keyword.trim()
    if(trimmed_keyword != '') {
      dispatch(search(trimmed_keyword))
    }
  }
}

export function search(keyword) {
  return (dispatch, getState) => {
    dispatch({
      type: types.SEARCH_KEYWORD,
      keyword
    })
    return setTimeout(() => {
      let searching_keyword = getState().index.searching_keyword
      if (searching_keyword != keyword) {
        return
      }
      return get(`/api/v1/topics/search?keyword=${keyword}`)
        .then((response) => {
          searching_keyword = getState().index.searching_keyword
          if (searching_keyword != keyword) {
            return
          }

          const topics_searched = response.data.data
          const next_search_url = response.data.paging.next

          dispatch({
            type: types.SEARCH_TOPICS,
            topics_searched,
            next_search_url
          })
        })
        .catch((response) => {
          /* eslint-disable no-console */
          console.log(response)
          /* eslint-enable no-console */
        })
    }, 1000)
  }
}
