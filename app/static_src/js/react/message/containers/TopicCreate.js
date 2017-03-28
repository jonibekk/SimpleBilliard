import { connect } from 'react-redux'
import * as actions from '~/message/actions/topic_create'
import TopicCreateComponent from '~/message/components/TopicCreate'

function mapStateToProps(state) {
  return { topic_create: state.topic_create}
}

function mapDispatchToProps(dispatch) {
  return {
    createTopic: () => dispatch(actions.createTopic()),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(TopicCreateComponent)
