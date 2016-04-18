import {FETCH_SETUP_STATUS} from '../constants/ActionTypes';
import axios from 'axios'

export function fetchSetupStatus() {
  let status = {}
  let setup_rest_count = 0
  axios.get('/setup/ajax_get_setup_status').then((response) => {
    console.log(response)
    status = response.data.status
    setup_rest_count = response.data.setup_rest_count
  }).catch((response) => {
  })
  return {
    type: FETCH_SETUP_STATUS,
    status: status,
    setup_rest_count: setup_rest_count,
  }
}

export function initSetupStatus() {
  return fetchSetupStatus()
}
