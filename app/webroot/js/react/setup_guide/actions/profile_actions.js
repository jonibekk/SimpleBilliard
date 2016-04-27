import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CAN_SUBMIT_PROFILE, CAN_NOT_SUBMIT_PROFILE, ADD_PROFILE } from '../constants/ActionTypes'

export function postProfile(dispatch, profile) {
  axios.post('/setup/ajax_add_profile', profile, {
    timeout: 10000,
    headers: {
      'Content-Type': 'application/json; charset=UTF-8'
    },
    dataType: 'json',
  })
  .then(function (response) {
    dispatch({
      type: ADD_PROFILE,
      form_input: profile
    })
    browserHistory.push('/setup')
  })
  .catch(function (response) {
    dispatch({
      type: ADD_PROFILE,
      form_input: profile
    })
    browserHistory.push('/setup')
  })
}

export function submitProfile(dispatch, event, refs) {
  event.preventDefault()
  let comment = ReactDOM.findDOMNode(refs.comment).value
  let profile_image = ReactDOM.findDOMNode(refs.profile_image).files[0]

  return postProfile(dispatch, {
    // _Token: cake.data.csrf_token.key,
    User: {
      photo: profile_image
    },
    TeamMember: {
      comment: comment
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
