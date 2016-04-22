import axios from 'axios'

export function postCircleCreate(circle) {
  var res
  axios.post('/setup/ajax_create_circle', circle)
  .then(function (response) {
  })
  .catch(function (response) {
  })
}
