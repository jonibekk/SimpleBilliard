import * as types from "~/message/constants/ActionTypes"
import { get } from "~/util/api"

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
      dispatch(search(trimmed_keyword))
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

export function search(keyword) {
  return (dispatch, getState) => {
    dispatch({
      type: types.SET_SEARCHING_KEYWORD,
      keyword
    })
    return setTimeout(() => {
      if (getState().search.current_searching_keyword != keyword) {
        return
      }

      return get(`/api/v1/topics/search?keyword=${keyword}`)
        .then((response) => {
          if (getState().search.current_searching_keyword != keyword) {
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
    }, 1000)
  }
}
