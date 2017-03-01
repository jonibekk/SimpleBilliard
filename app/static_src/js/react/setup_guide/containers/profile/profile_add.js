import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { toggleButtonClickable, submitProfile, fetchDefaultProfile, changedTextarea, enableSubmitButton } from '../../actions/profile_actions'
import ProfileAdd from '../../components/profile/profile_add'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    toggleButtonClickable: (profile) => {
      dispatch(toggleButtonClickable(profile))
      dispatch(changedTextarea())
    },
    onSubmitProfile: (profile) => { submitProfile(dispatch, profile) },
    fetchDefaultProfile: () => { fetchDefaultProfile(dispatch) },
    enableSubmitButton: () => { dispatch(enableSubmitButton()) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ProfileAdd);
