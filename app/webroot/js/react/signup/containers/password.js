import { connect } from 'react-redux'
import { postPassword, inputPassword, enableSubmitButton, disableSubmitButton, invalid, valid } from '../actions/password_actions'
import PasswordComponent from '../components/password'

function mapStateToProps(state) {
  return { password: state.password }
}

function mapDispatchToProps(dispatch) {
  return {
    inputPassword: (password) => dispatch(inputPassword(password)),
    postPassword: (password) => dispatch(postPassword(password)),
    enableSubmitButton: () => dispatch(enableSubmitButton()),
    disableSubmitButton: () => dispatch(disableSubmitButton()),
    invalid: (messages) => dispatch(invalid(messages)),
    valid: () => dispatch(valid())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PasswordComponent)
