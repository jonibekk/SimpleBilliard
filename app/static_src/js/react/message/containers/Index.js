import { connect } from 'react-redux'
import * as actions from '~/message/actions/index'
import IndexComponent from '~/message/components/Index'

function mapStateToProps(state) {
  return { topics: state.topics }
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(IndexComponent)
