import { FETCH_SETUP_STATUS } from '../constants/ActionTypes'
import axios from 'axios'

export function fetchSetupStatus(dispatch) {
  let status = {}
  let setup_rest_count = 0
  axios.get('/setup/ajax_get_setup_status').then((response) => {
    dispatch({
      type: FETCH_SETUP_STATUS,
      status: response.data.status,
      setup_rest_count: response.data.setup_rest_count,
    })
  }).catch((response) => {
  })
}

export function initSetupStatus() {
  return fetchSetupStatus()
}
