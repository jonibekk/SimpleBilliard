import React from 'react'
import { connect } from 'react-redux'
import { createCircle } from '../../actions/top_actions'
import Top from '../../components/top/top'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(Top);
