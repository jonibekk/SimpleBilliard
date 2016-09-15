import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";

const initialState = {
  toNextPage: false,
  categories:[],
  labels:[],
  terms:[],
  keyword: "",
  suggestions: [],
  validationErrors: {
    name: '',
    category: '',
    labels: '',
  },
  inputData:{}
}

export default function goal(state = initialState, action) {
  let inputData = state.inputData
  console.log("-------reducer start-------")
  console.log({action, state})
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
      console.log("FETCH_INITIAL_DATA")
      let newState = Object.assign({}, state, {toNextPage: false})
      // if (action.page == Page.STEP2) {
      //   return Object.assign({}, newState, {
      //     categories: action.data.categories,
      //     labels: action.data.labels,
      //     suggestions: action.data.labels,
      //   })
      // }
        return Object.assign({}, newState, action.data)
      return newState;
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
      inputData.labels = inputData.labels || [];
      inputData.labels.push(action.suggestion.name)
      return Object.assign({}, state, {
        inputData,
        keyword:""
      })
    case types.UPDATE_INPUT_DATA:
      inputData = Object.assign({}, state.inputData, action.data)
      return Object.assign({}, state, {
        inputData
      })
    default:
      return state;
  }
}
