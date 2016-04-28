import React from 'react'
import { connect } from 'react-redux'
import { fetchCircles, selectCircle, joinCircle } from '../../actions/circle_actions'
import CircleSelect from '../../components/circle/circle_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchCircles: () => { fetchCircles(dispatch) },
    onClickSelectCircle: (selected_circle_id) => { dispatch(selectCircle(selected_circle_id)) },
    onClickJoinCircle: (selected_circle_id) => { joinCircle(dispatch, selected_circle_id) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleSelect)
