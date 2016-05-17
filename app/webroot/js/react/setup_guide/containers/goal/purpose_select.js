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
    onClickSelectPurpose: (purpose) => {
      dispatch(selectPurpose(purpose))
      browserHistory.push('/setup/goal/select')
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PurposeSelect)
