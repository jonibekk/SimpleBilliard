import { connect } from 'react-redux'
import DetailComponent from '../components/detail'
import * as actions from '../actions/detail_actions'

function mapStateToProps(state) {
  return { detail: state.detail }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGaolApproval: goal_id => dispatch(actions.fetchGaolApproval(goal_id)),
    postSetAsTarget: post_data => dispatch(actions.postSetAsTarget(post_data)),
    postRemoveFromTarget: post_data => dispatch(actions.postRemoveFromTarget(post_data))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
