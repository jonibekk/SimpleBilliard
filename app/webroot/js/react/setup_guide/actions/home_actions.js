import {SETUP_STATUS_UPDATE} from '../constants/ActionTypes';
import axios from 'axios'

export function updateSetupStatus(status) {
  axios.get('/setup/ajax_get_setup_status').then((response) => {
    status = response.data.status
  }).catch((response) => {
  })
  return {
    type: SETUP_STATUS_UPDATE,
    status
  }
}
