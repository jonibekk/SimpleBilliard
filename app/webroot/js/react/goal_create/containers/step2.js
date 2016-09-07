import { connect } from 'react-redux'
import Step2Component from '../components/step2'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(Step2Component)
