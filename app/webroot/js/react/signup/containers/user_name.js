import { connect } from 'react-redux'
import * as actions from '../actions/user_name_actions'
import UserNameComponent from '../components/user_name'

function mapStateToProps(state) {
  return { user_name: state.user_name, validate: state.validate }
}

function mapDispatchToProps(dispatch) {
  return {
    postUserName: user => dispatch(actions.postUserName(user)),
    dispatch: action => dispatch(action)
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(UserNameComponent)
