import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'

export function selectNoDevices(dispatch) {
  axios.post('/setup/ajax_register_no_device', {}, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    },
    dataType: 'json',
  })
  .then(function (response) {
    if(response.data.error) {
    } else {
    }
  })
  .catch(function (response) {
  })
  document.location.href = "/setup/?from=splogin"
}
