import * as types from '../constants/ActionTypes'
import { get } from './common_actions'

const mock_response_data = {
  data:[
    {
      id: 1,
      name: 'sameple goal1',
      is_coach: true,
      user: {
        id: 1,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Taro'
      },
      collaborator: {
        id: 1,
        user_id: 1,
        type: 1,
        approval_status: 0
      }
    },
    {
      id: 2,
      name: 'sameple goal2',
      is_coach: true,
      user: {
        id: 2,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Hanako'
      },
      collaborator: {
        id: 2,
        user_id: 2,
        type: 0,
        approval_status: 1
      }
    },
    {
      id: 3,
      name: 'sameple goal3',
      is_coach: true,
      user: {
        id: 3,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Hanako'
      },
      collaborator: {
        id: 3,
        user_id: 3,
        type: 0,
        approval_status: 1
      }
    },
    {
      id: 4,
      name: 'sameple goal4',
      is_coach: true,
      user: {
        id: 4,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Hanako'
      },
      collaborator: {
        id: 4,
        user_id: 4,
        type: 0,
        approval_status: 1
      }
    },
    {
      id: 7,
      name: 'sameple goal7',
      is_coach: true,
      user: {
        id: 7,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Hanako'
      },
      collaborator: {
        id: 7,
        user_id: 7,
        type: 0,
        approval_status: 1
      }
    },
    {
      id: 5,
      name: 'sameple goal5',
      is_coach: true,
      user: {
        id: 5,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Hanako'
      },
      collaborator: {
        id: 5,
        user_id: 5,
        type: 0,
        approval_status: 1
      }
    },
    {
      id: 6,
      name: 'sameple goal6',
      is_coach: true,
      user: {
        id: 6,
        photo_file_name: 'http://static.tumblr.com/3e5d6a947659da567990fba7fd677358/qvo076m/sZKn744y4/tumblr_static_ah8scud0vgg0k4cco8s0gwogc.jpg',
        display_username: 'Test Hanako'
      },
      collaborator: {
        id: 6,
        user_id: 6,
        type: 0,
        approval_status: 1
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
