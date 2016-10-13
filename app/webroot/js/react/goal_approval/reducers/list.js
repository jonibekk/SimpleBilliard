import * as types from '../constants/ActionTypes'

const initialState = {
  goal_members: [],
  application_count: 0,
  fetching_goal_members: false,
  next_getting_api: null,
  // TODO: 第一フェーズではページネーションは行わないので全件表示する
  done_loading_all_data: true
}

export default function list(state = initialState, action) {
  switch (action.type) {
    case types.INIT_GOAL_MEMBERS:
      return Object.assign({}, state, {
        goal_members: action.goal_members
      })
    case types.ADD_GOAL_MEMBERS:
      return Object.assign({}, state, {
        goal_members: [...state.goal_members, ...action.goal_members]
      })
    case types.SET_APPLICATION_COUNT:
      return Object.assign({}, state, {
        application_count: action.application_count
      })
    case types.FETCHING_GOAL_MEMBERS:
      return Object.assign({}, state, {
        fetching_goal_members: true
      })
    case types.FINISHED_FETCHING_GOAL_MEMBERS:
      return Object.assign({}, state, {
        fetching_goal_members: false
      })
    case types.SET_NEXT_PAGING_API:
      return Object.assign({}, state, {
        next_getting_api: action.next_getting_api
      })
    case types.DONE_LOADING_ALL_DATA:
      return Object.assign({}, state, {
        done_loading_all_data: true
      })
    default:
      return state;
  }
}
