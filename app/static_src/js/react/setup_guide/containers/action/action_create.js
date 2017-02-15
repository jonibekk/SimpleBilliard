import React from 'react'
import { connect } from 'react-redux'
import { toggleButtonClickable, submitAction } from '../../actions/action_actions'
import ActionCreate from '../../components/action/action_create'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onChangeTextField: (refs) => {
      dispatch(toggleButtonClickable(refs))
    },
    onSubmitAction: (event, refs, socket_id, goal_id) => {
      event.preventDefault()
      submitAction(dispatch, refs, socket_id, goal_id)
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ActionCreate);
