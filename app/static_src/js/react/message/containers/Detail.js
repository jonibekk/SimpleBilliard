import { connect } from 'react-redux'
import * as actions from '~/message/actions/detail'
import DetailComponent from '~/message/components/Detail'

function mapStateToProps(state) {
  return { messages: state.messages }
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
