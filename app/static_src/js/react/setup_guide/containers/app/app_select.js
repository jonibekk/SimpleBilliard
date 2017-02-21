import React from 'react'
import { connect } from 'react-redux'
import { selectNoDevices } from '../../actions/app_actions'
import AppSelect from '../../components/app/app_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickNoDevices: () => {
      selectNoDevices()
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(AppSelect);
