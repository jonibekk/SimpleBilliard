import React from 'react'
import { connect } from 'react-redux'
import { createCircle, toggleButtonClickable } from '../../actions/circle_actions'
import CircleCreate from '../../components/circle/circle_create'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onSubmitCircle: (input_circle) => {
      createCircle(dispatch, input_circle)
    },
    toggleButtonClickable: (circle) => {
      dispatch(toggleButtonClickable(circle))
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleCreate);
