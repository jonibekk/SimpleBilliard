import teamNameReducer from '../../reducers/team_name'
import * as types from '../../constants/actionTypes'
import expect from 'expect'

describe('reducer::team_name', () => {
  it('INPUT_TEAM_NAME', () => {
    const expect_value = { inputed_team_name: 'あいうえお' }
    const newState = teamNameReducer({}, { type: types.INPUT_TEAM_NAME, inputed_team_name: 'あいうえお' });
    expect(newState).toInclude(expect_value)
  })
  it('TEAM_NAME_IS_INVALID', () => {
    const expect_value = { team_name_is_invalid: true, invalid_message: 'あいうえお' }
    const newState = teamNameReducer({}, { type: types.TEAM_NAME_IS_INVALID, invalid_message: 'あいうえお' });
    expect(newState).toInclude(expect_value)
  })
  it('CHECKING_INPUT_TEAM_NAME', () => {
    const expect_value = { checking_team_name: true }
    const newState = teamNameReducer({}, { type: types.CHECKING_INPUT_TEAM_NAME});
    expect(newState).toInclude(expect_value)
  })
})
