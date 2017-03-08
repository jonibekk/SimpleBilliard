import { connect } from 'react-redux'
import * as actions from '~/message/actions/topic_actions'
import TopicComponent from '~/message/components/Topic'

function mapStateToProps(state) {
  return { topics: state.topics }
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(TopicComponent)
