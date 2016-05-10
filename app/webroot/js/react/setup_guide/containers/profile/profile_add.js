import React from 'react'
import { Link, browserHistory } from 'react-router'
import { bindActionCreators } from 'redux'
import { connect } from 'react-redux'
import { toggleButtonClickable, submitProfile } from '../../actions/profile_actions'
import ProfileAdd from '../../components/profile/profile_add'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    toggleButtonClickable: (refs) => { dispatch(toggleButtonClickable(refs)) },
    onSubmitProfile: (event, refs) => {
      event.preventDefault()
      submitProfile(dispatch, refs)
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ProfileAdd);
