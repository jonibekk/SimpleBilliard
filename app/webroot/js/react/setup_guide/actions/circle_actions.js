import ReactDOM from 'react-dom'
import axios from 'axios'
import { CREATE_CIRCLE, SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE } from '../constants/ActionTypes'

export function postCircleCreate(dispatch, circle) {
  axios.post('/setup/ajax_create_circle', circle, {
    timeout: 10000,
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
  .then(function (response) {
    dispatch({
      type: CREATE_CIRCLE,
      form_input: circle.Circle
    })
  })
  .catch(function (response) {
    dispatch({
      type: CREATE_CIRCLE,
      form_input: circle.Circle
    })
  })
}

export function createCircle(dispatch, event, refs) {
  event.preventDefault()
  let circle_name = ReactDOM.findDOMNode(refs.circle_name).value
  let members = ReactDOM.findDOMNode(refs.members).value
  let public_flg = ReactDOM.findDOMNode(refs.public_flg).value
  let circle_description = ReactDOM.findDOMNode(refs.circle_description).value
  let circle_image = ReactDOM.findDOMNode(refs.circle_image).files[0]
  return postCircleCreate(dispatch, {
    _Token: cake.data.csrf_token.key,
    Circle: {
      circle_name: circle_name,
      members: members,
      public_flg: public_flg,
      circle_description: circle_description,
      circle_image: circle_image
    }
  })
}

export function selectCircle(circle_id) {
  return {
    type: SELECT_CIRCLE,
    selected_circle_id: circle_id
  }
}

export function fetchCircles(dispatch) {
  return axios.get('/setup/ajax_get_circles').then((response) => {
    var circles = response.data.not_joined_circles
    dispatch({
      type: FETCH_CIRCLES,
      circles: circles
    })
  }).catch((response) => {
    dispatch({
      type: FETCH_CIRCLES,
      circles: response
    })
  })
}

export function joinCircle(event, history, circle_id) {
  event.preventDefault();
  return axios.post('/setup/ajax_get_circles', circle_id).then((response) => {
    history.pushState(null, '/setup')
  }).catch((response) => {
    console.log(response)
    history.pushState(null, '/setup')
  })
}
