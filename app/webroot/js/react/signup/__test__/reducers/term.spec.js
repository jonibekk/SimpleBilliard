import termReducer from '../../reducers/team_name'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::term', () => {
  it('INPUT_TEAM_NAME', () => {
    const expect_value = { inputed_team_name: 'あいうえお' }
    const newState = teamNameReducer({}, { type: types.INPUT_TEAM_NAME, inputed_team_name: 'あいうえお' });
    expect(newState).toInclude(expect_value)
  })
})
