import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import * as actionCreators from '../../actions/circle_actions'
import CircleImage from '../../components/circle/circle_image'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return { actions: bindActionCreators({}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleImage);
