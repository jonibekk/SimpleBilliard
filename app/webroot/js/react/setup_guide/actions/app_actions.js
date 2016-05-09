import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { SELECT_NO_DEVICES } from '../constants/ActionTypes'

export function selectNoDevices(dispatch) {
  axios.post('/setup/ajax_register_no_device', {}, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    },
    dataType: 'json',
  })
  .then(function (response) {
    if(!response.data.res) {
      console.log(response)
    }
    dispatch({
      type: SELECT_NO_DEVICES,
      select_no_devices: true
    })
  })
  .catch(function (response) {
    console.log(response)
  })
}
