import { connect } from 'react-redux'
import * as actions from '../../actions/auth_actions'
import AuthComponent from '../../components/auth'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(AuthComponent)
