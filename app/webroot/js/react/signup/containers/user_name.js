import { connect } from 'react-redux'
import * as actions from '../actions/user_name_actions'
import UserNameComponent from '../components/user_name'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(UserNameComponent)
