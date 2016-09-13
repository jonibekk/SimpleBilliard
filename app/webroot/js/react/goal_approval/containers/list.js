import { connect } from 'react-redux'
import ListComponent from '../components/list'
import * as actions from '../actions/goal_approval_actions'

function mapStateToProps(state) {
  return { goal_approval: state.goal_approval }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGaolApprovals: (is_initialize = false) => { dispatch(actions.fetchGaolApprovals(is_initialize)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ListComponent)
