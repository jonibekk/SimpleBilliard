import * as types from '../constants/ActionTypes'

const initialState = {
  goal_member: {},
  to_list_page: false,
  posting_set_as_target: false,
  posting_remove_from_target: false,
  posting_withdraw: false,
  posting_comment: false,
  validationErrors: { comment: ''},
  add_comments: [],
  comment: ''
}

export default function detail(state = initialState, action) {
  switch (action.type) {
    case types.SET_GOAL_MEMBER:
      return Object.assign({}, state, {
        goal_member: action.goal_member
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
    case types.POSTING_WITHDRAW:
      return Object.assign({}, state, {
        posting_withdraw: true
      })
    case types.POSTING_COMMENT:
      return Object.assign({}, state, {
        posting_comment: true
      })
    case types.FINISHED_POSTING_COMMENT:
      return Object.assign({}, state, {
        posting_comment: false
      })
    case types.FINISHED_POSTING_REMOVE_FROM_TARGET:
      return Object.assign({}, state, {
        posting_remove_from_target: false
      })
    case types.FINISHED_POSTING_WITHDRAW:
      return Object.assign({}, state, {
        posting_withdraw: false
      })
    case types.INVALID:
      return Object.assign({}, state, {
        validationErrors: action.error.validation_errors
      })
    case types.INIT_DETAIL_PAGE:
      return Object.assign({}, initialState)
    case types.ADD_COMMENT:
      return Object.assign({}, state, {
        add_comments: [...state.add_comments, action.comment]
      })
    case types.UPDATE_COMMENT:
      return Object.assign({}, state, {
        comment: action.comment
      })
    default:
      return state;
  }
}
