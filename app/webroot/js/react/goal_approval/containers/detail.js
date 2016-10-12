import { connect } from 'react-redux'
import DetailComponent from '~/goal_approval/components/detail'
import * as actions from '~/goal_approval/actions/detail_actions'

function mapStateToProps(state) {
  return { detail: state.detail }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchCollaborator: (collaborator_id) => dispatch(actions.fetchCollaborator(collaborator_id)),
    postSetAsTarget: (input_data) => dispatch(actions.postSetAsTarget(input_data)),
    postRemoveFromTarget: (input_data) => dispatch(actions.postRemoveFromTarget(input_data)),
    initDetailPage: () => dispatch(actions.initDetailPage()),
    postWithdraw: (collaborator_id) => dispatch(actions.postWithdraw(collaborator_id))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
