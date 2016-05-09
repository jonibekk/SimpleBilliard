import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CAN_SUBMIT_PROFILE, CAN_NOT_SUBMIT_PROFILE, ADD_PROFILE } from '../constants/ActionTypes'

export function postProfile(dispatch, form_data) {
  axios.post('/setup/ajax_add_profile', form_data, {
    timeout: 10000, // 10sec
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
    });
    dispatch({
      type: ADD_PROFILE,
      form_input: profile
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
    browserHistory.push('/setup')
    dispatch({
      type: ADD_PROFILE,
      form_input: profile
    })
  })
}

export function submitProfile(dispatch, refs) {
  let form_data = new FormData()
  form_data.append("photo", ReactDOM.findDOMNode(refs.profile_image).files[0]);
  form_data.append("TeamMember[0][comment]", ReactDOM.findDOMNode(refs.comment).value);
  return postProfile(dispatch, form_data)
}

export function toggleButtonClickable(refs) {
  let comment = ReactDOM.findDOMNode(refs.comment).value
  let profile_image = ReactDOM.findDOMNode(refs.profile_image).files[0]
  if(!comment || !profile_image) {
    return {
      type: CAN_NOT_SUBMIT_PROFILE,
      can_click_submit_button: false
    }
  } else {
    return {
      type: CAN_SUBMIT_PROFILE,
      can_click_submit_button: true
    }
  }
}
