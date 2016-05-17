import React from 'react'
import { connect } from 'react-redux'
import { selectGoal } from '../../actions/goal_actions'
import GoalSelect from '../../components/goal/goal_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickSelectGoal: (goal) => { dispatch(selectGoal(goal)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(GoalSelect)
