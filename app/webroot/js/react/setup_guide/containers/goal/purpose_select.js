import React from 'react'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import { selectPurpose } from '../../actions/goal_actions'
import PurposeSelect from '../../components/goal/purpose_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickSelectPurpose: (purpose_name) => {
      dispatch(selectPurpose(purpose_name))
      browserHistory.push('/setup/goal/select')
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PurposeSelect)
