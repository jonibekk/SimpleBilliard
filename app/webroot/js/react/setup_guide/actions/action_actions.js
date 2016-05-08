import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { FETCH_GOALS, SELECT_ACTION_GOAL } from '../constants/ActionTypes'

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

export function selectActionGoal(goal_id) {
  return {
    type: SELECT_ACTION_GOAL,
    selected_action_goal: {
      id: goal_id
    }
  }
}
