import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CREATE_CIRCLE, SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE } from '../constants/ActionTypes'

export function postCircleCreate(dispatch, circle) {
  axios.post('/setup/ajax_create_circle', circle, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  })
  .then(function (response) {
    browserHistory.push('/setup')
    PNotify.removeAll()
    if(response.data.error) {
      new PNotify({
          type: 'error',
          title: cake.word.error,
          text: response.data.msg,
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
      })
    } else {
      new PNotify({
          type: 'success',
          title: cake.word.success,
          text: response.data.msg,
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
      })
    }
    dispatch({
      type: CREATE_CIRCLE,
      form_input: circle.Circle
    })
  })
  .catch(function (response) {
    browserHistory.push('/setup')
    PNotify.removeAll()
    new PNotify({
        type: 'error',
        title: cake.word.error,
        text: response.data.msg,
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    })
    dispatch({
      type: CREATE_CIRCLE,
      form_input: circle.Circle
    })
  })
}

export function createCircle(dispatch, refs) {
  let form_data = new FormData()
  form_data.append("photo", ReactDOM.findDOMNode(refs.circle_image).files[0])
  form_data.append("Circle[name]", ReactDOM.findDOMNode(refs.circle_name).value)
  form_data.append("Circle[public_flg]", ReactDOM.findDOMNode(refs.public_flg).value)
  form_data.append("Circle[description]", ReactDOM.findDOMNode(refs.circle_description).value)
  form_data.append("Circle[members]", ReactDOM.findDOMNode(refs.members).value)

  return postCircleCreate(dispatch, form_data)
}

export function selectCircle(circle_id) {
  return {
    type: SELECT_CIRCLE,
    selected_circle_id: circle_id
  }
}

export function fetchCircles(dispatch) {
  return axios.get('/setup/ajax_get_circles', {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  }).then((response) => {
    if(response.data.error) {
      dispatch({
        type: FETCH_CIRCLES,
        circles: []
      })
    } else {
      dispatch({
        type: FETCH_CIRCLES,
        circles: response.data.not_joined_circles
      })
    }
  }).catch((response) => {
    dispatch({
      type: FETCH_CIRCLES,
      circles: []
    })
  })
}

export function joinCircle(dispatch, circle_id) {
  const post_data = {
    'Circle': {
      0: {
        'circle_id': circle_id,
        'join': true
      }
    }
  }
  return axios.post('/setup/ajax_join_circle', post_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  }).then((response) => {
    browserHistory.push('/setup')
    PNotify.removeAll()
    if(response.data.error) {
      new PNotify({
          type: 'error',
          title: cake.word.error,
          text: response.data.msg,
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
      })
    } else {
      new PNotify({
          type: 'success',
          title: cake.word.success,
          text: response.data.msg,
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
      })
      dispatch({
        type: JOIN_CIRCLE,
        joined: true
      })
    }
  }).catch((response) => {
    browserHistory.push('/setup')
    new PNotify({
        type: 'error',
        title: cake.word.error,
        text: response.data.msg,
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    })
  })
}
