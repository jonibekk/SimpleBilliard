import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import ProfileImage from '../../components/profile/profile_image'

function mapStateToProps(state) {
  return state
}

export default connect(mapStateToProps)(ProfileImage);
