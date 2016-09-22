import * as types from '../constants/ActionTypes'

const initialState = {
  goal_approval: {},
  to_list_page: false,
  posting_set_as_target: false,
  posting_remove_from_target: false,
  validationErrors: { comment: '' }
}

export default function detail(state = initialState, action) {
  switch (action.type) {
    case types.SET_GOAL_APPROVAL:
      return Object.assign({}, state, {
        goal_approval: action.goal_approval
      })
    case types.TO_LIST_PAGE:
      return Object.assign({}, state, {
        to_list_page: true
      })
    case types.POSTING_SET_AS_TARGET:
      return Object.assign({}, state, {
        posting_set_as_target: true
      })
    case types.FINISHED_POSTING_SET_AS_TARGET:
      return Object.assign({}, state, {
        posting_set_as_target: false
      })
    case types.POSTING_REMOVE_FROM_TARGET:
      return Object.assign({}, state, {
        posting_remove_from_target: true
      })
    case types.FINISHED_POSTING_REMOVE_FROM_TARGET:
      return Object.assign({}, state, {
        posting_remove_from_target: false
      })
    case types.INVALID:
      return Object.assign({}, state, {
        validationErrors: action.error.validation_errors
      })
    case types.INIT_DETAIL_PAGE:
      return Object.assign({}, initialState)
    default:
      return state;
  }
}
