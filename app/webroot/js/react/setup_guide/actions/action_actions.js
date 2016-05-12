import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { FETCH_GOALS, SELECT_ACTION_GOAL, CAN_SUBMIT_ACTION, CAN_NOT_SUBMIT_ACTION } from '../constants/ActionTypes'

export function toggleButtonClickable(refs) {
  const body = ReactDOM.findDOMNode(refs.body).value.trim()
  return body ? enableSubmitButton() : disableSubmitButton()
}

export function enableSubmitButton() {
  return {
    type: CAN_SUBMIT_ACTION,
  }
}

export function disableSubmitButton() {
  return {
    type: CAN_NOT_SUBMIT_ACTION,
  }
}

export function fetchGoals(dispatch) {
  return axios.get('/setup/ajax_get_goals', {
      timeout: 10000,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
      dataType: 'json'
    }).then(function (response) {
      const goals = response.data.goals
      dispatch({
        type: FETCH_GOALS,
        goals: goals
      })
    })
    .catch(function (response) {
      console.log(response)
    })
}

export function selectActionGoal(goal) {
  return {
    type: SELECT_ACTION_GOAL,
    selected_action_goal: goal
  }
}
