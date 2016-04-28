import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CAN_SUBMIT_PROFILE, CAN_NOT_SUBMIT_PROFILE, ADD_PROFILE } from '../constants/ActionTypes'

export function postProfile(dispatch, profile) {
  axios.put('/setup/ajax_add_profile', profile, {
    timeout: 10000,
    headers: {
      'Content-Type': 'application/json; charset=UTF-8'
    },
    dataType: 'json',
  })
  .then(function (response) {
    console.log(response)
    PNotify.removeAll()
    dispatch({
      type: ADD_PROFILE,
      form_input: profile
    })
    new PNotify({
        type: 'success',
        title: cake.word.success,
        text: response.data.msg,
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    });
    browserHistory.push('/setup')
  })
  .catch(function (response) {
    dispatch({
      type: ADD_PROFILE,
      form_input: profile
    })
    new PNotify({
        type: 'success',
        title: cake.word.error,
        text: response.data.msg,
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    });
    browserHistory.push('/setup')
  })
}

export function submitProfile(dispatch, event, refs) {
  event.preventDefault()
  let comment = ReactDOM.findDOMNode(refs.comment).value
  let profile_image = ReactDOM.findDOMNode(refs.profile_image).files[0]

  return postProfile(dispatch, {
    // _Token: cake.data.csrf_token.key,
    // User: {
    //   photo: profile_image
    // },
    TeamMember: {
      0: {
        comment: comment
      }
    }
  })
}

export function toggleButtonClickable(dispatch, refs) {
  let comment = ReactDOM.findDOMNode(refs.comment).value
  let profile_image = ReactDOM.findDOMNode(refs.profile_image).files[0]
  if(!comment || !profile_image) {
    dispatch({
      type: CAN_NOT_SUBMIT_PROFILE,
      can_click_submit_button: false
    })
  } else {
    dispatch({
      type: CAN_SUBMIT_PROFILE,
      can_click_submit_button: true
    })
  }
}
