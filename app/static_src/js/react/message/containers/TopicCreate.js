import { connect } from 'react-redux'
import * as actions from '~/message/actions/topic_create'
import * as file_upload from '~/message/modules/file_upload'
import TopicCreateComponent from '~/message/components/TopicCreate'

function mapStateToProps(state) {
  return {
    topic_create: state.topic_create,
    file_upload: state.file_upload
  }
}

function mapDispatchToProps(dispatch) {
  return {
    createTopic: () => dispatch(actions.createTopic()),
    resetStates: () => dispatch(actions.resetStates()),
    uploadFiles: (files) => dispatch(file_upload.uploadFiles(files)),
    updateInputData: (input_data) => dispatch(actions.updateInputData(input_data)),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(TopicCreateComponent)
