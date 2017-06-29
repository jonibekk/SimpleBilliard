import axios from 'axios'
import { browserHistory } from 'react-router'
import { CAN_SUBMIT_PROFILE, CAN_NOT_SUBMIT_PROFILE, ADD_PROFILE, FETCH_DEFAULT_PROFILE, CHANGED_TEXTAREA } from '../constants/ActionTypes'

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

export function changedTextarea() {
  return {
    type: CHANGED_TEXTAREA,
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
    if(response.data.error) {
      new Noty({
        type: 'error',
        text: '<h4>'+cake.word.error+'</h4>'+__("Failed to save user setting."),
      }).show();
      dispatch(enableSubmitButton())
    } else {
      document.location.href = "/setup/?from=profile"
    }
  })
  .catch(function (response) {
    new Noty({
      type: 'error',
      text: '<h4>'+cake.word.error+'</h4>'+__("Failed to save user setting."),
    }).show();
    dispatch(enableSubmitButton())
  })
}

export function submitProfile(dispatch, input_profile) {
  let form_data = new FormData()
  form_data.append("TeamMember[0][comment]", input_profile.comment)
  // 画像がアップロードされずにsubmitされた場合は画像の更新をしない
  if(input_profile.profile_image) {
    form_data.append("photo", input_profile.profile_image)
  }
  return postProfile(dispatch, form_data)
}

export function toggleButtonClickable(input_profile) {
  return input_profile.comment ? enableSubmitButton() : disableSubmitButton()
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
      // do something...
    } else {
      dispatch({
        type: FETCH_DEFAULT_PROFILE,
        default_profile: response.data.default_profile
      })
    }
  }).catch((response) => {
  })
}
