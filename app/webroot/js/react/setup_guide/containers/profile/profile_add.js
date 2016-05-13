import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { toggleButtonClickable, submitProfile, fetchDefaultProfile } from '../../actions/profile_actions'
import ProfileAdd from '../../components/profile/profile_add'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    toggleButtonClickable: (profile) => { dispatch(toggleButtonClickable(profile)) },
    onSubmitProfile: (event, refs) => {
      event.preventDefault()
      submitProfile(dispatch, refs)
    },
    fetchDefaultProfile:() => {
      fetchDefaultProfile(dispatch)
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ProfileAdd);
