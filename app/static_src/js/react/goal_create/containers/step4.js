import { connect } from 'react-redux'
import Step4Component from '../components/step4'
import * as actions from "../actions/goal_actions";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    saveGoal: () => dispatch(actions.saveGoal())
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(Step4Component)
