import axios from 'axios'
import { browserHistory } from 'react-router'
import { SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE, CAN_SUBMIT_CIRCLE, CAN_NOT_SUBMIT_CIRCLE, CAN_JOIN_CIRCLE, CAN_NOT_JOIN_CIRCLE } from '../constants/ActionTypes'

export function toggleButtonClickable(circle) {
  return circle.circle_name && circle.circle_description ? enableSubmitButton() : disableSubmitButton()
}

export function enableSubmitButton() {
  return {
    type: CAN_SUBMIT_CIRCLE
  }
}

export function disableSubmitButton() {
  return {
    type: CAN_NOT_SUBMIT_CIRCLE
  }
}

export function enableJoinCircleButton() {
  return {
    type: CAN_JOIN_CIRCLE
  }
}

export function disableJoinCircleButton() {
  return {
    type: CAN_NOT_JOIN_CIRCLE
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
    if(response.data.error) {
      new Noty({
        type: 'error',
        text: '<h4>'+cake.word.error+'</h4>'+response.data.msg,
      }).show();
    } else {
      document.location.href = "/setup/?from=circle&type=create"
    }
  })
  .catch(function (response) {
    browserHistory.push('/setup')
    new Noty({
      type: 'error',
      text: '<h4>'+cake.word.error+'</h4>'+response.data.msg,
    }).show();
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

export function selectCircle(dispatch, selected_circle_id_list, selected_circle_id) {
  if(selected_circle_id_list.indexOf(selected_circle_id) >= 0) {
    selected_circle_id_list = selected_circle_id_list.filter(function(value){
        return value != selected_circle_id;
    });
  } else {
    selected_circle_id_list.push(selected_circle_id)
  }
  if(selected_circle_id_list.length > 0) {
    dispatch(enableJoinCircleButton())
  } else {
    dispatch(disableJoinCircleButton())
  }
  dispatch({
    type: SELECT_CIRCLE,
    selected_circle_id_list: selected_circle_id_list
  })
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

export function joinCircle(dispatch, circle_id_list) {
  dispatch(disableJoinCircleButton())
  let post_data = {
    Circle: {
    }
  }
  for (var i = 0; i < circle_id_list.length; i++) {
    post_data.Circle[i] = {
      'circle_id': circle_id_list[i],
      'join': true
    }
  }
  return axios.post('/setup/ajax_join_circle', post_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  }).then((response) => {
    if(response.data.error) {
      new Noty({
        type: 'error',
        text: '<h4>'+cake.word.error+'</h4>'+response.data.msg,
      }).show();
      dispatch(enableJoinCircleButton())
    } else {
      document.location.href = "/setup/?from=circle&type=join"
    }
  }).catch((response) => {
    browserHistory.push('/setup')
    new Noty({
      type: 'error',
      text: '<h4>'+cake.word.error+'</h4>'+response.data.msg,
    }).show();
    dispatch(enableJoinCircleButton())
  })
}
