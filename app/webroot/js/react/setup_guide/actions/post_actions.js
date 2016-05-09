import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { SELECT_CIRCLE_FOR_POST } from '../constants/ActionTypes'

export function selectCirclePost(circle_id) {
  return {
    type: SELECT_CIRCLE_FOR_POST,
    selected_circle_id_for_post: circle_id
  }
}

export function fetchCirclesForPost(dispatch) {
  return axios.get('/setup/ajax_get_circles_for_post', {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
      dataType: 'json'
    }).then(function (response) {
      const circles = response.data.circles
      dispatch({
        type: SELECT_CIRCLE_FOR_POST,
        circles: circles
      })
    })
    .catch(function (response) {
      console.log(response)
    })
}
