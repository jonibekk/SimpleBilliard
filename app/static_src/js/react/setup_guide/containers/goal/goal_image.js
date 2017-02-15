import React from 'react'
import { connect } from 'react-redux'
import GoalImage from '../../components/goal/goal_image'

function mapStateToProps(state) {
  return state
}

export default connect(mapStateToProps)(GoalImage)
