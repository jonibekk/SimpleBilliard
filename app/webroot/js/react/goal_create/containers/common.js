import * as actions from "../actions/goal_actions";

export function getCommonDispatchToProps(dispatch) {
  return {
    validateGoal: (page) => dispatch(actions.validateGoal(page)),
    fetchInitialData: (page) => dispatch(actions.fetchInitialData(page)),
    updateInputData: (data) => dispatch(actions.updateInputData(data))
  }
}

