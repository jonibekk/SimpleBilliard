import axios from 'axios'
import { browserHistory } from 'react-router'
import { SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE, CAN_SUBMIT_CIRCLE, CAN_NOT_SUBMIT_CIRCLE } from '../constants/ActionTypes'

export function toggleButtonClickable(circle) {
  return circle.circle_name && circle.circle_description ? enableSubmitButton() : disableSubmitButton()
}

export function enableSubmitButton() {
  return {
    type: CAN_SUBMIT_CIRCLE,
  }
}

export function disableSubmitButton() {
  return {
    type: CAN_NOT_SUBMIT_CIRCLE,
  }
}

export function postCircleCreate(dispatch, circle) {
  axios.post('/setup/ajax_create_circle', circle, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  })
  .then(function (response) {
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
      document.location.href = "/setup"
    }
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
  })
}

export function createCircle(dispatch, input_circle) {
  let form_data = new FormData()
  form_data.append("photo", input_circle['circle_image'])
  form_data.append("Circle[name]", input_circle['circle_name'])
  form_data.append("Circle[public_flg]", input_circle['public_flg'])
  form_data.append("Circle[description]", input_circle['circle_description'])
  form_data.append("Circle[members]", input_circle['members'])

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
      browserHistory.push('/setup/circle/create')
    } else {
      if(response.data.not_joined_circles.length === 0) {
        browserHistory.push('/setup/circle/create')
      } else {
        dispatch({
          type: FETCH_CIRCLES,
          circles: response.data.not_joined_circles
        })
      }
    }
  }).catch((response) => {
    browserHistory.push('/setup/circle/create')
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
      document.location.href = "/setup"
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
