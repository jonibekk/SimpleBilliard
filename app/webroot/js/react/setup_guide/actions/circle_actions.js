import ReactDOM from 'react-dom'
import axios from 'axios'
import { CREATE_CIRCLE, SELECT_CIRCLE, FETCH_CIRCLES } from '../constants/ActionTypes'

export function postCircleCreate(circle) {
  var res
  axios.post('/setup/ajax_create_circle', circle)
  .then(function (response) {
  })
  .catch(function (response) {
  })
}

export function createCircle(event, refs) {
  event.preventDefault()
  let circle_name = ReactDOM.findDOMNode(refs.circle_name).value
  let members = ReactDOM.findDOMNode(refs.members).value
  let public_flg = ReactDOM.findDOMNode(refs.public_flg).value
  let circle_description = ReactDOM.findDOMNode(refs.circle_description).value
  let circle_image = ReactDOM.findDOMNode(refs.circle_image).files[0]
  // const res = postCircleCreate({
  //   _Token: cake.data.csrf_token.key,
  //   body: {
  //     circle_name: circle_name,
  //     members: members,
  //     public_flg: public_flg,
  //     circle_description: circle_description,
  //     circle_image: circle_image
  //   }
  // })
  return {
    type: CREATE_CIRCLE,
    circle_name: circle_name,
    members: members,
    public_flg: public_flg,
    circle_description: circle_description,
    circle_image: circle_image
  }
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
