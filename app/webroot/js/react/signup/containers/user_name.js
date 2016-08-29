import { connect } from 'react-redux'
import * as actions from '../actions/user_name_actions'
import UserNameComponent from '../components/user_name'

function mapStateToProps(state) {
  return { user_name: state.user_name }
}

function mapDispatchToProps(dispatch) {
  return {
    postUserName: user => dispatch(actions.postUserName(user)),
    inputUserName: user => dispatch(actions.inputUserName(user)),
    invalid: element => dispatch(actions.invalid(element)),
    valid: element => dispatch(actions.valid(element))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(UserNameComponent)
