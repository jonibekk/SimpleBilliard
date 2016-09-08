import { connect } from 'react-redux'
import ListComponent from '../components/list'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(ListComponent)
