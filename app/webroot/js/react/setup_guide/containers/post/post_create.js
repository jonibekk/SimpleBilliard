import React from 'react'
import { connect } from 'react-redux'
import { toggleButtonClickable, fetchFileUploadFormElement, submitPost } from '../../actions/post_actions'
import PostCreate from '../../components/post/post_create'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchFileUploadFormElement: () => { fetchFileUploadFormElement(dispatch) },
    onSubmitPost: (event, refs, socket_id) => {
      event.preventDefault()
      submitPost(dispatch, refs, socket_id)
    },
    toggleButtonClickable: (refs) => { dispatch(toggleButtonClickable(refs)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PostCreate);
