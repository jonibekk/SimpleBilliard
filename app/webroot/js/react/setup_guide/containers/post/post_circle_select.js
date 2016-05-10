import React from 'react'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import { selectCirclePost, fetchCirclesForPost } from '../../actions/post_actions'
import PostCircleSelect from '../../components/post/post_circle_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    selectCirclePost: (circle) => {
      dispatch(selectCirclePost(circle))
      browserHistory.push('/setup/post/create')
    },
    fetchCirclesForPost: () => { fetchCirclesForPost(dispatch) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PostCircleSelect)
