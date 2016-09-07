import { connect } from 'react-redux'
import Step1Component from '../components/step1'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(Step1Component)
