import React from 'react'
import { Link } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import AppImage from '../../components/app/app_image'

function mapStateToProps(state) {
  return state
}

export default connect(mapStateToProps)(AppImage);
