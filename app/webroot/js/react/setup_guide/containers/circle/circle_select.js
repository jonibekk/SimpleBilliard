import React from 'react'
import { Link, browserHistory } from 'react-router'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { selectCircle } from '../../actions/circle_actions'
import CircleSelect from '../../components/circle/circle_select'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onClickSelectCircle: (selected_circle_id) => { dispatch(selectCircle(selected_circle_id)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleSelect);
