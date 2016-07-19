import { connect } from 'react-redux'
import * as actions from '../../actions/password_actions'
import PasswordComponent from '../../components/password'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(PasswordComponent)
