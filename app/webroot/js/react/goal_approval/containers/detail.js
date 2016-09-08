import { connect } from 'react-redux'
import DetailComponent from '../components/detail'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
