import { connect } from 'react-redux';
import * as actions from '~/message/actions/search_messages';
import SearchMessagesComponent from '~/message/components/SearchMessages';

function mapStateToProps(state) {
  return {
    index: state.index,
    search: state.search
  }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitData: () => dispatch(actions.fetchInitData()),
    fetchMore: (url) => dispatch(actions.fetchMore(url)),
    setUaInfo: () => dispatch(actions.setUaInfo()),
    initLayout: () => dispatch(actions.initLayout())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(SearchMessagesComponent)
