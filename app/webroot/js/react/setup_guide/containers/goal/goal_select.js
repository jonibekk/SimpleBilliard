import React from 'react'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import { selectGoal } from '../../actions/goal_actions'
import GoalSelect from '../../components/goal/goal_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickSelectGoal: (goal_name) => {
      dispatch(selectGoal(goal_name))
      browserHistory.push('/setup/goal/create')
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(GoalSelect)
