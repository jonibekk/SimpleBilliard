import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CREATE_GOAL, SELECT_PURPOSE, SELECT_GOAL, INITIALIZE_SELECTED_GOAL } from '../constants/ActionTypes';

export function selectPurpose(purpose) {
  return {
    type: SELECT_PURPOSE,
    selected_purpose: purpose
  }
}

export function selectGoal(goal) {
  return {
    type: SELECT_GOAL,
    selected_goal: goal
  }
}

export function createGoal(goal) {
  let form_data = new FormData()
  form_data.append("photo", goal.photo)
  form_data.append("Goal[name]", goal.name)
  form_data.append("Goal[value_unit]", goal.value_unit)
  form_data.append("Goal[start_value]", goal.start_value)
  form_data.append("Goal[target_value]", goal.target_value)
  form_data.append("Goal[start_date]", goal.start_date)
  form_data.append("Goal[end_date]", goal.end_date)
  form_data.append("Goal[img_url]", goal.img_url)
  form_data.append("Purpose[name]", goal.purpose_name)
  axios.post('/setup/ajax_create_goal', form_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    },
    dataType: 'json',
  })
  .then(function (response) {
    if(response.data.error) {
      browserHistory.push('/setup')
      PNotify.removeAll()
      new PNotify({
          type: 'error',
          title: cake.word.error,
          text: __("Failed to add an action."),
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
      })
    } else {
      document.location.href = "/setup/?from=goal"
    }
  })
  .catch(function (response) {
    browserHistory.push('/setup')
    PNotify.removeAll()
    new PNotify({
        type: 'error',
        title: cake.word.error,
        text: __("Failed to add an action."),
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    })
  })
  return {
    type: CREATE_GOAL
  }
}

export function initSelectedGoalData() {
  return {
    type: INITIALIZE_SELECTED_GOAL
  }
}
