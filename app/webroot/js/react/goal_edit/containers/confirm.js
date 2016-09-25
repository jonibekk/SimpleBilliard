import {connect} from "react-redux";
import Confirm from "../components/confirm";
import * as actions from "../actions/goal_actions";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchComments: () => dispatch(actions.fetchComments()),
    addLabel: (label) => dispatch(actions.addLabel(label)),
    saveGoal: (addInputData) => dispatch(actions.saveGoal(addInputData)),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Confirm)

