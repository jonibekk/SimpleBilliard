import React from 'react'
import { connect } from 'react-redux'
import { selectPurpose, initSelectedGoalData } from '../../actions/goal_actions'
import PurposeSelect from '../../components/goal/purpose_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickSelectPurpose: (purpose) => { dispatch(selectPurpose(purpose)) },
    initSelectedGoalData: () => { dispatch(initSelectedGoalData()) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PurposeSelect)
