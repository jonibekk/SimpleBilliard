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
    onClickSelectCircle: (selected_circle_id_list, selected_circle_id) => {
      selectCircle(dispatch, selected_circle_id_list, selected_circle_id)
    },
    onClickJoinCircle: (selected_circle_id_list) => { joinCircle(dispatch, selected_circle_id_list) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleSelect)
