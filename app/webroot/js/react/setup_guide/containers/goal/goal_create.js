import React from 'react'
import { connect } from 'react-redux'
import GoalCreate from '../../components/goal/goal_create'
import { createGoal } from '../../actions/goal_actions'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onSubmit: (event, refs) => {
      event.preventDefault()
      dispatch(createGoal(refs))
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(GoalCreate)
