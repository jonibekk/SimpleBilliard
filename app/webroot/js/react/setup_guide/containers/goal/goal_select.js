import React from 'react'
import { connect } from 'react-redux'
import GoalSelect from '../../components/goal/goal_select'

function mapStateToProps(state) {
  return state
}

export default connect(mapStateToProps)(GoalSelect)
