import * as actions from "../actions/index";

export function getCommonDispatchToProps(dispatch) {
  return {
    validatePayment: (page, add_data = {}) => dispatch(actions.validatePayment(page, add_data)),
    fetchInitialData: (page) => dispatch(actions.fetchInitialData(page)),
    updateInputData: (data, key = "") => dispatch(actions.updateInputData(data, key)),
    resetStates: () => dispatch(actions.resetStates()),
  }
}

