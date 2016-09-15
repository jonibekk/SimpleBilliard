import { connect } from 'react-redux'
import EditComponent from '../components/edit'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(EditComponent)
