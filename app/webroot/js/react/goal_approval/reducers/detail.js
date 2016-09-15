import * as types from '../constants/ActionTypes'

const initialState = {
  goal_approval: {
    id: 1,
    name: 'goal name',
    category: {
      name: 'category name'
    },
    key_result: {
      id: 1,
      name: 'key result name'
    },
    collaborator: {
      role: 'leader',
      user: {
        name: 'user name'
      }
    }
  }
}

export default function detail(state = initialState, action) {
  switch (action.type) {
    case types.SET_GOAL_APPROVAL:
      return Object.assign({}, state, {
        goal_approval: action.goal_approval
      })
    default:
      return state;
  }
}
