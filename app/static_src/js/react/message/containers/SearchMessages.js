import { connect } from 'react-redux';
import * as actions from '~/message/actions/search_messages';
import SearchMessagesComponent from '~/message/components/SearchMessages';

function mapStateToProps(state) {
  return {
    search_messages: state.search_messages
  }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitialData: (topic_id, search_conditions) => dispatch(actions.fetchInitialData(topic_id, search_conditions)),
    fetchMore: (url) => dispatch(actions.fetchMore(url)),
    setUaInfo: () => dispatch(actions.setUaInfo()),
    initLayout: () => dispatch(actions.initLayout()),
    setResourceId: (topic_id) => dispatch(actions.setResourceId(topic_id)),
    reset: (topic_id) => dispatch(actions.reset(topic_id)),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(SearchMessagesComponent)
