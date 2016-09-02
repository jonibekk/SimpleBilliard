import { connect } from 'react-redux'
import * as actions from '../actions/password_actions'
import PasswordComponent from '../components/password'

function mapStateToProps(state) {
  return { password: state.password, validate: state.validate }
}

function mapDispatchToProps(dispatch) {
  return {
    postPassword: password => dispatch(actions.postPassword(password)),
    invalid: element => dispatch(actions.invalid(element)),
    valid: element => dispatch(actions.valid(element)),
    dispatch: (action) => dispatch(action)
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PasswordComponent)
