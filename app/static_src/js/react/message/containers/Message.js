import { connect } from 'react-redux'
import * as actions from '~/message/actions/message_actions'
import MessageComponent from '~/message/components/Message'

function mapStateToProps(state) {
  return { messages: state.messages }
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(MessageComponent)
