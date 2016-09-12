import * as types from '../constants/ActionTypes'
import { get } from './common_actions'

const mock_response_data = {
  data:[
    {
      id: 1,
      name: 'sameple goal1',
      user: {
        id: 1,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Taro'
      },
      collaborator: {
        id: 1,
        user_id: 1,
        type: 1,
        approval_status: 1
      }
    },
    {
      id: 2,
      name: 'sameple goal2',
      user: {
        id: 2,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Hanako'
      },
      collaborator: {
        id: 2,
        user_id: 2,
        type: 0,
        approval_status: 0
      }
    }
  ]
}

export function fetchGaolApprovals() {
  return dispatch => {
    dispatch(fetchingGoalApprovals())
    return get('/mock', response => {
      dispatch(finishedFetchingGoalApprovals())
    }, () => {
      dispatch(finishedFetchingGoalApprovals())
      dispatch(setGoalApprovals(mock_response_data.data))
    })
  }
}

export function setGoalApprovals(goal_approvals) {
  return { type: types.SET_GOAL_APPROVALS, goal_approvals }
}

export function fetchingGoalApprovals() {
  return { type: types.FETCHING_GOAL_APPROVALS }
}

export function finishedFetchingGoalApprovals() {
  return { type: types.FINISHED_FETCHING_GOAL_APPROVALS }
}
