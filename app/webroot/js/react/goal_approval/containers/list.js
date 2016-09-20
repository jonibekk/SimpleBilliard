import { connect } from 'react-redux'
import ListComponent from '../components/list'
import * as actions from '../actions/goal_approval_actions'

function mapStateToProps(state) {
  return { list: state.list }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGoalApprovals: (is_initialize = false) => { dispatch(actions.fetchGoalApprovals(is_initialize)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ListComponent)
