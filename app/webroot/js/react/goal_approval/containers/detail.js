import {connect} from 'react-redux'
import DetailComponent from '~/goal_approval/components/detail'
import * as actions from '~/goal_approval/actions/detail_actions'

function mapStateToProps(state) {
  return {detail: state.detail}
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGoalMember: (goal_member_id) => dispatch(actions.fetchGoalMember(goal_member_id)),
    postSetAsTarget: (input_data) => dispatch(actions.postSetAsTarget(input_data)),
    postRemoveFromTarget: (input_data) => dispatch(actions.postRemoveFromTarget(input_data)),
    initDetailPage: () => dispatch(actions.initDetailPage()),
    postWithdraw: (goal_member_id) => dispatch(actions.postWithdraw(goal_member_id)),
    saveComment: (postData) => dispatch(actions.saveComment(postData))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
