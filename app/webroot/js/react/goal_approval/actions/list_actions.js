import * as types from '~/goal_approval/constants/ActionTypes'
import axios from "axios"

export function fetchCollaborators() {
  return (dispatch) => {
    dispatch(fetchingCollaborators())
    return axios.get('/api/v1/goal_approvals/list', {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Cache-Control': 'no-store, private, no-cache, must-revalidate'
      },
      dataType: 'json'
    })
    .then((response) => {
      dispatch(finishedFetchingCollaborators())
      dispatch(setFetchData(response.data.data))
      /* eslint-disable no-console */
      console.log('fetch init data')
      /* eslint-enable no-console */
    })
    .catch(() => {
      dispatch(finishedFetchingCollaborators())
    })

  }
}

export function setFetchData(fetch_data) {
  return { type: types.SET_FETCH_DATA, fetch_data }
}

export function fetchingCollaborators() {
  return { type: types.FETCHING_COLLABORATORS }
}

export function finishedFetchingCollaborators() {
  return { type: types.FINISHED_FETCHING_COLLABORATORS }
}
