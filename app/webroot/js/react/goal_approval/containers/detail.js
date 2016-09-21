import { connect } from 'react-redux'
import DetailComponent from '../components/detail'
import * as actions from '../actions/detail_actions'

function mapStateToProps(state) {
  return { detail: state.detail }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGaolApproval: goal_id => dispatch(actions.fetchGaolApproval(goal_id)),
    postSetAsTarget: input_data => dispatch(actions.postSetAsTarget(input_data)),
    postRemoveFromTarget: input_data => dispatch(actions.postRemoveFromTarget(input_data)),
    initDetailPage: () => dispatch(actions.initDetailPage())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
