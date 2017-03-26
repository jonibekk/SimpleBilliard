import { connect } from 'react-redux'
import * as actions from '~/message/actions/index'
import IndexComponent from '~/message/components/Index'

function mapStateToProps(state) {
  return {
    index: state.index
  }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitData: () => dispatch(actions.fetchInitData()),
    fetchMoreTopics: (url) => dispatch(actions.fetchMoreTopics(url)),
    changeToSearchMode: () => dispatch(actions.changeToSearchMode()),
    cancelSearchMode: () => dispatch(actions.cancelSearchMode()),
    fetchMoreSearchTopics: (url) => dispatch(actions.fetchMoreSearchTopics(url)),
    inputSearchKeyword: (keyword) => dispatch(actions.inputSearchKeyword(keyword))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(IndexComponent)
