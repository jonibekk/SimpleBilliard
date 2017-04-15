import { connect } from 'react-redux'
import * as actions from '~/message/actions/topic_members_add'
import TopicMembersAddComponent from '~/message/components/TopicMembersAdd'

function mapStateToProps(state) {
  return {
    topic_members_add: state.topic_members_add,
  }
}

function mapDispatchToProps(dispatch) {
  return {
    setResourceId: (topic_id) => dispatch(actions.setResourceId(topic_id)),
    selectUsers: (user_ids) => dispatch(actions.selectUsers(user_ids)),
    addMembers: () => dispatch(actions.addMembers()),
    resetStates: () => dispatch(actions.resetStates()),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(TopicMembersAddComponent)
