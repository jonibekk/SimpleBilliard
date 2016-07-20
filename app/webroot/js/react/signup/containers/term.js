import { connect } from 'react-redux'
import * as actions from '../actions/term_actions'
import TermComponent from '../components/term'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(TermComponent)
