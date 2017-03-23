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
