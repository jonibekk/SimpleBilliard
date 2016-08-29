import { connect } from 'react-redux'
import * as actions from '../actions/password_actions'
import PasswordComponent from '../components/password'

function mapStateToProps(state) {
  return { password: state.password }
}

function mapDispatchToProps(dispatch) {
  return {
    inputPassword: (password) => dispatch(actions.inputPassword(password)),
    postPassword: (password) => dispatch(actions.postPassword(password)),
    invalid: (messages) => dispatch(actions.invalid(messages)),
    valid: () => dispatch(actions.valid())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PasswordComponent)
