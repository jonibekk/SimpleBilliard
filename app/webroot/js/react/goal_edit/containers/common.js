import * as actions from "../actions/goal_actions";

export function getCommonDispatchToProps(dispatch) {
  return {
    validateGoal: (addData = {}) => dispatch(actions.validateGoal(addData)),
    fetchInitialData: (goalId) => dispatch(actions.fetchInitialData(goalId)),
    updateInputData: (data, key = "") => dispatch(actions.updateInputData(data, key))
  }
}

