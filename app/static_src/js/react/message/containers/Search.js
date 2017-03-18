import { connect } from 'react-redux'
import * as actions from '~/message/actions/index'
import SearchComponent from '~/message/components/Search'

function mapStateToProps(state) {
  return { topics: state.topics }
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(SearchComponent)
