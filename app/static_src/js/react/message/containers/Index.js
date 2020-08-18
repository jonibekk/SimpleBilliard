import { connect } from 'react-redux'
import * as index_actions from '~/message/actions/index'
import * as search_actions from '~/message/actions/search'
import IndexComponent from '~/message/components/Index'

function mapStateToProps(state) {
  return {
    index: state.index,
    search: state.search
  }
}

function mapDispatchToProps(dispatch) {
  return {
    // index
    fetchInitData: () => dispatch(index_actions.fetchInitData()),
    fetchMore: (url) => dispatch(index_actions.fetchMore(url)),
    changeToSearchMode: () => dispatch(index_actions.changeToSearchMode()),
    cancelSearchMode: () => dispatch(index_actions.cancelSearchMode()),
    // search
    inputKeyword: (keyword) => dispatch(search_actions.inputKeyword(keyword)),
    changeSearchType: (type) => dispatch(search_actions.changeSearchType(type)),
    searchData: (search_conditions) => dispatch(search_actions.search(search_conditions)),
    fetchMoreSearch: (url) => dispatch(search_actions.fetchMoreSearch(url)),
    emptyTopics: () => dispatch(search_actions.emptyTopics()),
    setUaInfo: () => dispatch(index_actions.setUaInfo()),
    init_completed: () => dispatch(index_actions.init_completed())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(IndexComponent)