import { connect } from 'react-redux'
import { postUserName } from '../actions/user_name_actions'
import UserNameComponent from '../components/user_name'

function mapStateToProps(state) {
  return {user_name: state.user_name}
}

function mapDispatchToProps(dispatch) {
  return {
    postUserName: (user) => { dispatch(postUserName(user)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(UserNameComponent)
