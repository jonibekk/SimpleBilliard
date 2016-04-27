import { FETCH_SETUP_STATUS } from '../constants/ActionTypes'
import axios from 'axios'

export function fetchSetupStatus(dispatch) {
  let status = {}
  let setup_rest_count = 0
  axios.get('/setup/ajax_get_setup_status').then((response) => {
    let complete_percent = 0
    if(response.data.setup_rest_count !== 0) {
      complete_percent = Math.round(100 * ((6 - response.data.setup_rest_count) / 6))
    }
    dispatch({
      type: FETCH_SETUP_STATUS,
      status: response.data.status,
      setup_rest_count: response.data.setup_rest_count,
      setup_complete_percent: complete_percent
    })
  }).catch((response) => {
  })
}
