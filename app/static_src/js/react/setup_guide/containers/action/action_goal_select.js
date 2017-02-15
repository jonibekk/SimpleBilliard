import React from 'react'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import { fetchGoals, selectActionGoal } from '../../actions/action_actions'
import ActionGoalSelect from '../../components/action/action_goal_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGoals: () => { fetchGoals(dispatch) },
    onClickSelectActionGoal: (goal_id) => {
      dispatch(selectActionGoal(goal_id))
      browserHistory.push('/setup/action/create')
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ActionGoalSelect)
