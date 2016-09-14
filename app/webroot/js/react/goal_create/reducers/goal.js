import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";

const initialState = {
  // page: Page.STEP1,
  toNextPage: false,
  categories:[],
  labels:[],
  keyword: "",
  suggestions: [],
  validationErrors: {
    name: '',
    category: '',
    labels: '',
  },
  inputData:{
    name:"",
    category:null,
    labels:[],
  }
}

export default function goal(state = initialState, action) {
  let inputData = state.inputData
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, {
        validationErrors: action.error.validation_errors
      })
    case types.TO_NEXT_PAGE:
      // 現在のページを基に次のページを返却
      return Object.assign({}, state, {
        toNextPage: true
      })
    case types.FETCH_INITIAL_DATA:
      return Object.assign({}, state, {
        categories: action.data.categories,
        labels: action.data.labels,
        suggestions: action.data.labels,
      })
    case types.REQUEST_SUGGEST:
      return Object.assign({}, state, {
        suggestions: action.suggestions,
        keyword: action.keyword
      })
    case types.CLEAR_SUGGEST:
      return Object.assign({}, state)
    case types.SET_KEYWORD:
      return Object.assign({}, state, {
        keyword: action.keyword
      })
    case types.SELECT_SUGGEST:
      inputData.labels.push(action.suggestion.name)
      return Object.assign({}, state, {
        inputData:inputData,
        keyword:""
      })
    case types.UPDATE_INPUT_DATA:
      inputData = Object.assign({}, state.inputData, action.data)
      return Object.assign({}, state, {
        inputData:inputData,
      })
    default:
      return state;
  }
}
