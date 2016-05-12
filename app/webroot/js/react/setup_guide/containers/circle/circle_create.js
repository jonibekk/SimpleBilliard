import React from 'react'
import { connect } from 'react-redux'
import { createCircle } from '../../actions/circle_actions'
import CircleCreate from '../../components/circle/circle_create'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onSubmitCircle: (event, refs) => {
      event.preventDefault()
      createCircle(dispatch, refs)
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleCreate);
