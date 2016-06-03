import { FETCH_SETUP_STATUS } from '../constants/ActionTypes'
import axios from 'axios'

export function fetchSetupStatus(dispatch) {
  let status = {}
  let setup_rest_count = 0
  axios.get('/setup/ajax_get_setup_status', {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    },
    dataType: 'json',
  })
  .then(function (response) {
    let complete_percent = 0
    dispatch({
      type: FETCH_SETUP_STATUS,
      status: response.data.status,
      setup_rest_count: response.data.rest_count,
      setup_complete_percent: response.data.complete_percent
    })
  }).catch((response) => {
  })

}
