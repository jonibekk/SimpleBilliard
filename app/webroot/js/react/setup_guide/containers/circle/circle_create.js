import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { createCircle } from '../../actions/circle_actions'
import CircleCreate from '../../components/circle/circle_create'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    handleSubmit: (e, refs) => { dispatch(createCircle(e, refs)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleCreate);
