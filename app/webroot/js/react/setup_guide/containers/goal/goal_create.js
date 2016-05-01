import React from 'react'
import { connect } from 'react-redux'
import GoalCreate from '../../components/goal/goal_create'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onSubmit: (event) => {
      createGoal(dispatch, event)
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(GoalCreate)
