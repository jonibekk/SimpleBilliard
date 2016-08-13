import { connect } from 'react-redux'
import { postPassword, inputPassword } from '../actions/password_actions'
import PasswordComponent from '../components/password'

function mapStateToProps(state) {
  return { password: state.password }
}

function mapDispatchToProps(dispatch) {
  return {
    inputPassword: (password) => dispatch(inputPassword(password)),
    postPassword: (password) => dispatch(postPassword(password))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PasswordComponent)
