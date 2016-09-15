import * as actions from '../../actions/goal_approval_actions'
import * as types from '../../constants/ActionTypes'
import nock from 'nock'
import expect from 'expect'

describe('actions::goal_approval', () => {
  afterEach(() => {
    nock.cleanAll()
  })

  it('init goal approvals', () => {
    expect(actions.initGoalApprovals([{id: 1, name: 'test1 goal'}, {id: 2, name: 'test2 goal'}])).toEqual({ type: types.INIT_GOAL_APPROVALS, goal_approvals: [{id: 1, name: 'test1 goal'}, {id: 2, name: 'test2 goal'}] })
  })

  it('add goal approvals', () => {
    expect(actions.addGoalApprovals([{id: 1, name: 'test1 goal'}, {id: 2, name: 'test2 goal'}])).toEqual({ type: types.ADD_GOAL_APPROVALS, goal_approvals: [{id: 1, name: 'test1 goal'}, {id: 2, name: 'test2 goal'}] })
  })

  it('fetching goal approvals', () => {
    expect(actions.fetchingGoalApprovals()).toEqual({ type: types.FETCHING_GOAL_APPROVALS })
  })

  it('finished fetching goal approvals', () => {
    expect(actions.finishedFetchingGoalApprovals()).toEqual({ type: types.FINISHED_FETCHING_GOAL_APPROVALS })
  })

  it('set next getting api', () => {
    expect(actions.setNextPagingApi('/path/to/api')).toEqual({ type: types.SET_NEXT_PAGING_API, next_getting_api: '/path/to/api' })
  })

})
