import React from 'react'
import { connect } from 'react-redux'
import { selectGoal, initSelectedGoalData } from '../../actions/goal_actions'
import GoalSelect from '../../components/goal/goal_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickSelectGoal: (goal) => { dispatch(selectGoal(goal)) },
    initSelectedGoalData: () => { dispatch(initSelectedGoalData()) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(GoalSelect)
