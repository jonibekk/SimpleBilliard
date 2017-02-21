import * as types from '../constants/ActionTypes'

const initialState = {
  fetch_data: {
    goal_members: [],
    all_approval_count: 0,
    application_info: ''
  },
  fetching_goal_members: false,
  next_getting_api: null,
  // TODO: 第一フェーズではページネーションは行わないので全件表示する
  done_loading_all_data: true
}

export default function list(state = initialState, action) {
  switch (action.type) {
    case types.SET_FETCH_DATA:
      return Object.assign({}, state, {
        fetch_data: action.fetch_data
      })
    case types.FETCHING_GOAL_MEMBERS:
      return Object.assign({}, state, {
        fetching_goal_members: true
      })
    case types.FINISHED_FETCHING_GOAL_MEMBERS:
      return Object.assign({}, state, {
        fetching_goal_members: false
      })
    default:
      return state;
  }
}
