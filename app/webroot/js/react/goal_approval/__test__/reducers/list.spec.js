import ListReducer from '../../reducers/list'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::list', () => {
  it('INIT_GOAL_APPROVALS', () => {
    const expect_value = [
      {
        goal_field1: 'test',
        goal_field2: 'test',
        goal_field3: 'test'
      },
      {
        goal_field1: 'test2',
        goal_field2: 'test2',
        goal_field3: 'test2'
      }
    ]
    const newState = ListReducer({goal_approvals: []}, {
      type: types.INIT_GOAL_APPROVALS,
      goal_approvals: [
        {
          goal_field1: 'test',
          goal_field2: 'test',
          goal_field3: 'test'
        },
        {
          goal_field1: 'test2',
          goal_field2: 'test2',
          goal_field3: 'test2'
        }
      ]
    })

    expect(newState.goal_approvals).toEqual(expect_value)
  })

  it('ADD_GOAL_APPROVALS', () => {
    const expect_value = [
      {
        goal_field1: 'test',
        goal_field2: 'test',
        goal_field3: 'test'
      },
      {
        goal_field1: 'test2',
        goal_field2: 'test2',
        goal_field3: 'test2'
      }
    ]
    const newState = ListReducer({goal_approvals: []}, {
      type: types.ADD_GOAL_APPROVALS,
      goal_approvals: [
        {
          goal_field1: 'test',
          goal_field2: 'test',
          goal_field3: 'test'
        },
        {
          goal_field1: 'test2',
          goal_field2: 'test2',
          goal_field3: 'test2'
        }
      ]
    })

    expect(newState.goal_approvals).toEqual(expect_value)
  })

  it('SET_INCOMPLETE_COUNT', () => {
    const expect_value = {
      incomplete_count: 4
    }
    const newState = ListReducer({}, {
      type: types.SET_INCOMPLETE_COUNT,
      incomplete_count: 4
    })

    expect(newState).toInclude(expect_value)
  })

  it('FETCHING_GOAL_APPROVALS', () => {
    const expect_value = {
      fetching_goal_approvals: true
    }
    const newState = ListReducer({}, {
      type: types.FETCHING_GOAL_APPROVALS
    })

    expect(newState).toInclude(expect_value)
  })

  it('FINISHED_FETCHING_GOAL_APPROVALS', () => {
    const expect_value = {
      fetching_goal_approvals: false
    }
    const newState = ListReducer({}, {
      type: types.FINISHED_FETCHING_GOAL_APPROVALS
    })

    expect(newState).toInclude(expect_value)
  })

  it('SET_NEXT_PAGING_API', () => {
    const expect_value = {
      next_getting_api: '/path/to/api'
    }
    const newState = ListReducer({}, {
      type: types.SET_NEXT_PAGING_API,
      next_getting_api: '/path/to/api'
    })

    expect(newState).toInclude(expect_value)
  })

  it('DONE_LOADING_ALL_DATA', () => {
    const expect_value = {
      done_loading_all_data: true
    }
    const newState = ListReducer({}, {
      type: types.DONE_LOADING_ALL_DATA
    })

    expect(newState).toInclude(expect_value)
  })

})
