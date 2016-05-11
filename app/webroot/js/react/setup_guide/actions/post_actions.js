import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { SELECT_CIRCLE_FOR_POST, FETCH_CIRCLES_FOR_POST, FETCH_FILE_UPLOAD_ELEMENT } from '../constants/ActionTypes'

export function selectCirclePost(circle) {
  return {
    type: SELECT_CIRCLE_FOR_POST,
    selected_circle: circle
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
        type: FETCH_CIRCLES_FOR_POST,
        circles: circles
      })
    })
    .catch(function (response) {
      console.log(response)
    })
}

export function fetchFileUploadFormElement(dispatch) {
  return axios.get('/setup/ajax_get_file_upload_form_element', {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
      dataType: 'json'
    }).then(function (response) {
      const html = response.data.html
      dispatch({
        type: FETCH_FILE_UPLOAD_ELEMENT,
        file_upload_html: html
      })
    })
    .catch(function (response) {
      console.log(response)
    })
}

export function submitPost(dispatch, refs) {
  let form_data = new FormData()
  form_data.append("Post[body]", ReactDOM.findDOMNode(refs.post_body).value);
  form_data.append("Post[share_public]", ReactDOM.findDOMNode(refs.share_public).value);
  form_data.append("Post[share_secret]", ReactDOM.findDOMNode(refs.share_secret).value);
  axios.post('/posts/ajax_add_post_for_setup_guide', form_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  })
  .then(function (response) {
    browserHistory.push('/setup')
    PNotify.removeAll()
    new PNotify({
        type: 'success',
        title: cake.word.success,
        text: response.data.msg,
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    })
  })
  .catch(function (response) {
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
