import React from 'react'
import { connect } from 'react-redux'
import {  } from '../../actions/circle_actions'
import ActionCreate from '../../components/action/action_create'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ActionCreate);
