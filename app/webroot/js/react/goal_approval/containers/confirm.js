import { connect } from 'react-redux'
import ConfirmComponent from '../components/confirm'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(ConfirmComponent)
