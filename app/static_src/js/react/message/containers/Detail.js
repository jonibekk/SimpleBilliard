import { connect } from 'react-redux'
import * as actions from '~/message/actions/detail'
import DetailComponent from '~/message/components/Detail'

function mapStateToProps(state) {
  return {
    detail: state.detail,
    file_upload: state.file_upload
  }
}

function mapDispatchToProps(dispatch) {
  return {
    setResourceId: (topic_id) => dispatch(actions.setResourceId(topic_id)),
    fetchInitialData: (topic_id) => dispatch(actions.fetchInitialData(topic_id)),
    fetchMoreMessages: (url) => dispatch(actions.fetchMoreMessages(url)),
    sendMessage: () => dispatch(actions.sendMessage()),
    sendLike: () => dispatch(actions.sendLike()),
    onChangeMessage: (val) => dispatch(actions.onChangeMessage(val)),
    resetStates: () => dispatch(actions.resetStates())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
