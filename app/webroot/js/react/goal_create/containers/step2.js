import { connect } from 'react-redux'
import Step2Component from '../components/step2'
import * as actions from "../actions/goal_actions";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitialData: () => dispatch(actions.fetchInitialData())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Step2Component)
