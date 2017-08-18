import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { FETCH_GOALS, SELECT_ACTION_GOAL, CAN_SUBMIT_ACTION, CAN_NOT_SUBMIT_ACTION, FETCHED_GOALS } from '../constants/ActionTypes'

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
      if(goals.length > 0) {
        dispatch({
          type: FETCH_GOALS,
          goals: goals
        })
      }
      dispatch({
        type: FETCHED_GOALS
      })
    })
    .catch(function (response) {
      dispatch({
        type: FETCHED_GOALS
      })
    })
}

export function selectActionGoal(goal) {
  return {
    type: SELECT_ACTION_GOAL,
    selected_action_goal: goal
  }
}

export function submitAction(dispatch, refs, socket_id, goal_id) {
  let form_data = new FormData()
  const files = $('#CommonActionDisplayForm').find('[name="data[file_id][]"]')
  const file_limit_num = 11
  form_data.append("ActionResult[name]", ReactDOM.findDOMNode(refs.body).value.trim())
  form_data.append("ActionResult[goal_id]", goal_id)
  form_data.append("socket_id", socket_id)
  Array.from(Array(file_limit_num).keys()).map((i) => {
    if(files[i] === undefined) return
    form_data.append("file_id[]", files[i].value)
  })

  axios.post('/setup/ajax_add_action', form_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  })
  .then(function (response) {
    let msg = ''
    if(response.data.error) {
      browserHistory.push('/setup')
      new Noty({
        type: 'error',
        text: '<h4>'+cake.word.error+'</h4>'+this.response.data.msg,
      }).show();
    } else {
      document.location.href = "/setup/?from=action"
    }

  })
  .catch(function (response) {
    browserHistory.push('/setup')
    new Noty({
        type: 'error',
        text: '<h4>'+cake.word.error+'</h4>'+__("Failed to add an action."),
    }).show();
  })
}
