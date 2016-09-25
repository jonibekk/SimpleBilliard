import * as types from '../constants/ActionTypes'

const initialState = {
  collaborators: [],
  application_count: 0,
  fetching_collaborators: false,
  next_getting_api: null,
  done_loading_all_data: false
}

export default function list(state = initialState, action) {
  switch (action.type) {
    case types.INIT_COLLABORATORS:
      return Object.assign({}, state, {
        collaborators: action.collaborators
      })
    case types.ADD_COLLABORATORS:
      return Object.assign({}, state, {
        collaborators: [...state.collaborators, ...action.collaborators]
      })
    case types.SET_APPLICATION_COUNT:
      return Object.assign({}, state, {
        application_count: action.application_count
      })
    case types.FETCHING_COLLABORATORS:
      return Object.assign({}, state, {
        fetching_collaborators: true
      })
    case types.FINISHED_FETCHING_COLLABORATORS:
      return Object.assign({}, state, {
        fetching_collaborators: false
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
