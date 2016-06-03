import React from 'react'
import { connect } from 'react-redux'
import { createCircle } from '../../actions/top_actions'
import Top from '../../components/top/top'
import { fetchSetupStatus } from '../../actions/top_actions'
import { fetchGoals } from '../../actions/action_actions'

function mapStateToProps(state) {
  return {
    top: state.top,
    action: state.action
  }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchSetupStatus: () => {fetchSetupStatus(dispatch)},
    fetchGoals: () => {fetchGoals(dispatch)}
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Top);
