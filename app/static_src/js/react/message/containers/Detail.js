import { connect } from 'react-redux'
import * as actions from '~/message/actions/detail'
import DetailComponent from '~/message/components/Detail'

function mapStateToProps(state) {
  return { detail: state.detail}
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitialData: (topic_id) => dispatch(actions.fetchInitialData(topic_id)),
    fetchMoreMessages: (url) => dispatch(actions.fetchMoreMessages(url)),
    sendLike: (topic_id) => dispatch(actions.sendLike(topic_id))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
