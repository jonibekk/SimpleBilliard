import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import ProfileAdd from '../../components/profile/profile_add'

function mapStateToProps(state) {
  return state
}

export default connect(mapStateToProps)(ProfileAdd);
