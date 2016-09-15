import { connect } from 'react-redux'
import DetailComponent from '../components/detail'
import * as actions from '../actions/detail_actions'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGaolApproval: (goal_id) => dispatch(actions.fetchGaolApproval(goal_id))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
