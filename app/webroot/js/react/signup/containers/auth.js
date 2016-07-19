import { connect } from 'react-redux'
import { inputCode } from '../actions/auth_actions'
import AuthComponent from '../components/auth'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    inputCode: (index, code) => { dispatch(inputCode(index, code)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(AuthComponent)
