import React from 'react'
import { connect } from 'react-redux'
import PurposeSelect from '../../components/goal/purpose_select'

function mapStateToProps(state) {
  return state
}

export default connect(mapStateToProps)(PurposeSelect);
