import ReactDOM from 'react-dom'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { CREATE_GOAL, SELECT_PURPOSE, SELECT_GOAL } from '../constants/ActionTypes';

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

export function createGoal(refs) {
  let form_data = new FormData()
  form_data.append("photo", ReactDOM.findDOMNode(refs.photo).files[0]);
  form_data.append("Goal[name]", ReactDOM.findDOMNode(refs.name).value);
  form_data.append("Goal[value_unit]", ReactDOM.findDOMNode(refs.value_unit).value);
  form_data.append("Goal[start_value]", ReactDOM.findDOMNode(refs.start_value).value);
  form_data.append("Goal[target_value]", ReactDOM.findDOMNode(refs.target_value).value);
  form_data.append("Goal[start_date]", cake.current_term_start_date_format)
  form_data.append("Goal[end_date]", ReactDOM.findDOMNode(refs.end_date).value);
  form_data.append("Purpose[name]", ReactDOM.findDOMNode(refs.purpose_name).value);
  axios.post('/setup/ajax_create_goal', form_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    },
    dataType: 'json',
  })
  .then(function (response) {
    browserHistory.push('/setup')
    PNotify.removeAll()
    new PNotify({
        type: 'success',
        title: cake.word.success,
        text: response.data.msg,
        icon: "fa fa-check-circle",
        delay: 4000,
        mouse_reset: false
    })
  })
  .catch(function (response) {
    console.log(response)
  })
  return {
    type: CREATE_GOAL
  }
}
