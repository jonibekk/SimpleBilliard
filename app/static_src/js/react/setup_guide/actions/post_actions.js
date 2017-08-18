import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CAN_SUBMIT_POST, CAN_NOT_SUBMIT_POST, SELECT_CIRCLE_FOR_POST, FETCH_CIRCLES_FOR_POST, FETCH_FILE_UPLOAD_ELEMENT, SET_VALIDATION_ERRORS } from '../constants/ActionTypes'

export function toggleButtonClickable(refs) {
  const post_body = ReactDOM.findDOMNode(refs.post_body).value.trim()
  return post_body ? enableSubmitButton() : disableSubmitButton()
}

export function enableSubmitButton() {
  return {
    type: CAN_SUBMIT_POST,
  }
}

export function disableSubmitButton() {
  return {
    type: CAN_NOT_SUBMIT_POST,
  }
}

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

export function submitPost(dispatch, refs, socket_id) {
  let form_data = new FormData()
  form_data.append("Post[body]", ReactDOM.findDOMNode(refs.post_body).value.trim());
  form_data.append("Post[share_public]", ReactDOM.findDOMNode(refs.share_public).value);
  form_data.append("Post[share_secret]", ReactDOM.findDOMNode(refs.share_secret).value);
  form_data.append("Post[share_range]", ReactDOM.findDOMNode(refs.share_range).value);
  form_data.append("socket_id", socket_id);
  const files = $('#PostDisplayForm').find('[name="data[file_id][]"]')
  const file_limit_num = 10
  Array.from(Array(file_limit_num).keys()).map((i) => {
    if(files[i] === undefined) return
    form_data.append("file_id[]", files[i].value)
  })
  axios.post('/posts/ajax_add_post_for_setup_guide', form_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  })
  .then(function (response) {
    let msg = ''
    if(response.data.error) {
      if(response.data.validation_errors.length > 0) {
        dispatch(setValidationErrors(response.data.validation_errors))
        dispatch(enableSubmitButton())
      } else {
        browserHistory.push('/setup')
        new Noty({
          type: 'error',
          text: '<h4>'+cake.word.error+'</h4>'+response.data.msg,
        }).show();
      }
    } else {
      document.location.href = "/setup/?from=post"
    }
  })
  .catch(function (response) {
    browserHistory.push('/setup')
    new Noty({
      type: 'error',
      text: '<h4>'+cake.word.error+'</h4>'+__("Failed to post."),
    }).show();
  })
}

export function setValidationErrors(errors) {
  return {
    type: SET_VALIDATION_ERRORS,
    validation_errors: errors
  }
}
