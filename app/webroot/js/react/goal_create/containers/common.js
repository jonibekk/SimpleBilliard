import * as actions from "../actions/goal_actions";

export function getCommonDispatchToProps(dispatch) {
  return {
    validateGoal: (page, addData = {}) => dispatch(actions.validateGoal(page, addData)),
    fetchInitialData: (page) => dispatch(actions.fetchInitialData(page)),
    updateInputData: (data, key = "") => dispatch(actions.updateInputData(data, key))
  }
}

