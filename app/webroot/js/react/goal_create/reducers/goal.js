import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";

const initialState = {
  page: Page.STEP1,
  categories:[],
  labels:[],
  validationErrors: {
    name: '',
  }
}

export default function goal(state = initialState, action) {
  console.log("reducer start")
  console.log("------state------")
  console.log(state)
  console.log("------action------")
  console.log(action)
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
      console.log("fetch")
      console.log(action.data.categories)
      return Object.assign({}, state, {
        categories: action.data.categories,
        labels: action.data.labels
      })
    default:
      return state;
  }
}
