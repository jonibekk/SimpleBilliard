import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";

const initialState = {
  page: Page.STEP1,
  validationErrors: {
    name: '',
  }
}

export default function goal(state = initialState, action) {
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, {
        validationErrors: action.error.validation_errors
      })
    case types.TO_NEXT_PAGE:
      // 現在のページを基に次のページを返却
      const idx = Page.PAGE_FLOW.indexOf(state.page);
      return Object.assign({}, state, {
        page: Page.PAGE_FLOW[idx + 1]
      })
    case types.FETCH_INITIAL_DATA:
      return Object.assign({}, state, {
        categories: action.categories,
        labels: action.labels
      })
    default:
      return state;
  }
}
