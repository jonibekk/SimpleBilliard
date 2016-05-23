import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import {  } from '../../actions/action_actions'
import ActionImage from '../../components/action/action_image'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ActionImage);
