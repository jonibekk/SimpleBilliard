import React from 'react'
import { browserHistory } from 'react-router'
import { connect } from 'react-redux'
import { selectNoDevices } from '../../actions/app_actions'
import AppSelect from '../../components/app/app_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickNoDevices: () => {
      selectNoDevices(dispatch)
      browserHistory.push('/setup/')
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(AppSelect);
