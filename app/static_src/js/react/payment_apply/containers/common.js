import * as actions from "../actions/index";

export function getCommonDispatchToProps(dispatch) {
  return {
    validatePayment: (page, addData = {}) => dispatch(actions.validatePayment(page, addData)),
    fetchInitialData: (page) => dispatch(actions.fetchInitialData(page)),
    updateInputData: (data, key = "") => dispatch(actions.updateInputData(data, key)),
    resetStates: () => dispatch(actions.resetStates()),
  }
}

