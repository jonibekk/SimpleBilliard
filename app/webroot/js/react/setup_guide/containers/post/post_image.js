import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import * as actionCreators from '../../actions/post_actions'
import PostImage from '../../components/post/post_image'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return { actions: bindActionCreators({}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(PostImage);
