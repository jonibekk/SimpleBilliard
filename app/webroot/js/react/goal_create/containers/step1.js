import {connect} from "react-redux";
import Step1Component from "../components/step1";
import * as actions from "../actions/goal_actions";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    validateGoal: () => dispatch(actions.validateGoal()),
    updateInputData: (data) => dispatch(actions.updateInputData(data)),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Step1Component)

