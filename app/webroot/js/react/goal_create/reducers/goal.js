import * as types from "../constants/ActionTypes";
import * as pages from "../constants/Pages";

const initialState = {
  validationErrors: {
    name: ''
  }
}

export default function goal(state = initialState, action) {
  switch (action.type) {
    case types.VALIDATE_GOAL:
      return Object.assign({}, state)
    case types.INVALID:
      return Object.assign({}, state, {
        validationErrors: action.error.validation_errors
      })
    case types.TO_NEXT_PAGE:
      // 現在のページを基に次のページを返却
      const idx = pages.PAGE_FLOW.indexOf(state.page);
      return Object.assign({}, state, {
        toNextPage: pages.PAGE_FLOW[idx + 1]
      })
    default:
      return state;
  }
}
