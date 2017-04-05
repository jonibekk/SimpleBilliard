import * as types from "~/message/constants/ActionTypes";
import { get } from "~/util/api";

export function fetchInitData() {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING
    })
    return get('/api/v1/topics/search')
      .then((response) => {
        const topics = response.data.data
        const next_url = response.data.paging.next
        dispatch({
          type: types.INITIALIZE,
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

export function fetchMore(url) {
  return (dispatch) => {
    dispatch({
      type: types.FETCHING
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
      type: types.INITIALIZE_SEARCH
    })
    dispatch({
      type: types.CHANGE_TO_INDEX_MODE
    })
  }
}

export function updateTopicListItem(index, data) {
  return (dispatch) => {
    dispatch({
      type: types.UPDATE_TOPIC_LIST_ITEM,
      index,
      data
    })
    dispatch({
      type: types.UPDATE_TOPIC_SEARCH_LIST_ITEM,
      index,
      data
    })
  }
}
