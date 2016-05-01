import { SUBMIT_GOAL, SELECT_PURPOSE, SELECT_GOAL } from '../constants/ActionTypes';
import axios from 'axios'

export function selectPurpose(purpose_name) {
  return {
    type: SELECT_PURPOSE,
    selected_purpose: {
      name: purpose_name
    }
  }
}

export function selectGoal(goal_name) {
  return {
    type: SELECT_GOAL,
    selected_goal: {
      name: goal_name
    }
  }
}

export function submitGoal(goal) {
  axios.get('/setup/ajax_get_setup_status').then((response) => {
    status = response.data.status
  }).catch((response) => {
  })
  return {
    type: SETUP_STATUS_UPDATE,
    status
  }
}
