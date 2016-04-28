import React from 'react'
import { connect } from 'react-redux'
import GoalCreate from '../../components/goal/goal_create'

function mapStateToProps(state) {
  return state
}

export default connect(mapStateToProps)(GoalCreate)
