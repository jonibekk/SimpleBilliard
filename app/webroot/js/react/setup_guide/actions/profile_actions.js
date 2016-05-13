import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CAN_SUBMIT_PROFILE, CAN_NOT_SUBMIT_PROFILE, ADD_PROFILE, FETCH_DEFAULT_PROFILE } from '../constants/ActionTypes'

export function enableSubmitButton() {
  return {
    type: CAN_SUBMIT_PROFILE,
  }
}

export function disableSubmitButton() {
  return {
    type: CAN_NOT_SUBMIT_PROFILE,
  }
}

export function postProfile(dispatch, form_data) {
  axios.post('/setup/ajax_add_profile', form_data, {
    timeout: 10000, // 10sec
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
          text: __("Failed to save user setting."),
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
      })
    } else {
      document.location.href = "/setup"
    }
  })
  .catch(function (response) {
    dispatch(enableSubmitButton())
    PNotify.removeAll()
    new PNotify({
        type: 'error',
        title: cake.word.error,
        text: __("Failed to save user setting."),
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    })
  })
}

export function submitProfile(dispatch, refs) {
  let form_data = new FormData()
  if(ReactDOM.findDOMNode(refs.profile_image).files[0]) {
    form_data.append("photo", ReactDOM.findDOMNode(refs.profile_image).files[0])
  }
  form_data.append("TeamMember[0][comment]", ReactDOM.findDOMNode(refs.comment).value.trim())
  return postProfile(dispatch, form_data)
}

export function toggleButtonClickable(profile) {
  if(profile.comment) {
    return enableSubmitButton()
  } else {
    return disableSubmitButton()
  }
}

export function fetchDefaultProfile(dispatch) {
  return axios.get('/setup/ajax_get_default_user_profile', {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  }).then((response) => {
    if(response.data.error) {
    } else {
      dispatch({
        type: FETCH_DEFAULT_PROFILE,
        default_profile: response.data.default_profile
      })
    }
  }).catch((response) => {
  })
}
