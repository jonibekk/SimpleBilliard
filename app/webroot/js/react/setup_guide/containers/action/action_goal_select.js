import React from 'react'
import { connect } from 'react-redux'
import {  } from '../../actions/action_actions'
import ActionGoalSelect from '../../components/action/action_goal_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ActionGoalSelect)
