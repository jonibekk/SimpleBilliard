import goalReducer from '../../reducers/goal'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::goal', () => {
  it('LOAD_GOAL', () => {
    const expect_value = {
      goal_field1: 'test',
      goal_field2: 'test',
      goal_field3: 'test'
    }
    const newState = goalReducer({goals: []}, {
      type: types.LOAD_GOAL,
      goal: {
        goal_field1: 'test',
        goal_field2: 'test',
        goal_field3: 'test'
      }
    })

    expect(newState.goals).toInclude(expect_value)
  })

  it('SET_INCOMPLETE', () => {
    const expect_value = {
      incomplete_count: 4
    }
    const newState = goalReducer({}, {
      type: types.SET_INCOMPLETE,
      incomplete_count: 4
    })

    expect(newState).toInclude(expect_value)
  })

})
