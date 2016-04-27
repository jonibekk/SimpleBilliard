import React from 'react'
import { connect } from 'react-redux'
import { createCircle } from '../../actions/top_actions'
import Top from '../../components/top/top'
import { fetchSetupStatus } from '../../actions/top_actions'

function mapStateToProps(state) {
  return { top: state.top }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchSetupStatus: () => {fetchSetupStatus(dispatch)}
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Top);
