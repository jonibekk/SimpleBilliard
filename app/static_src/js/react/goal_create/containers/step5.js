import { connect } from 'react-redux'
import Step5Component from '../components/step5'
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

export default connect(mapStateToProps, mapDispatchToProps)(Step5Component)
