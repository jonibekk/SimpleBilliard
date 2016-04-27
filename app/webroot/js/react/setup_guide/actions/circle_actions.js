import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CREATE_CIRCLE, SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE } from '../constants/ActionTypes'

export function postCircleCreate(dispatch, circle) {
  axios.post('/setup/ajax_create_circle', circle, {
    timeout: 10000,
    headers: {
      'Content-Type': 'application/json; charset=UTF-8'
    },
    dataType: 'json',
  })
  .then(function (response) {
    dispatch({
      type: CREATE_CIRCLE,
      form_input: circle.Circle
    })
    browserHistory.push('/setup')
  })
  .catch(function (response) {
    dispatch({
      type: CREATE_CIRCLE,
      form_input: circle.Circle
    })
    browserHistory.push('/setup')
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
    // _Token: cake.data.csrf_token.key,
    Circle: {
      name: circle_name,
      public_flg: public_flg,
      description: circle_description,
      members: members
    },
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

export function joinCircle(dispatch, circle_id) {
  let post_data = {
    'Circle': {
      0: {
        'circle_id': circle_id,
        'join': true
      }
    }
  }
  return axios.post('/setup/ajax_join_circle', post_data).then((response) => {
    dispatch({
      type: JOIN_CIRCLE,
      joined: true
    })
    browserHistory.push('/setup')
  }).catch((response) => {
    browserHistory.push('/setup')
  })
}
