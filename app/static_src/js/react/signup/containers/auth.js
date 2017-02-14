import { connect } from 'react-redux'
import * as actions from '../actions/auth_actions'
import AuthComponent from '../components/auth'

function mapStateToProps(state) {
  return { auth: state.auth }
}

function mapDispatchToProps(dispatch) {
  return {
    inputCode: (index, code) => { dispatch(actions.inputCode(index, code)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(AuthComponent)
