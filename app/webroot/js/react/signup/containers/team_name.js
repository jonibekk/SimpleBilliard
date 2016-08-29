import { connect } from 'react-redux'
import * as actions from '../actions/team_name_actions'
import TeamNameComponent from '../components/team_name'

function mapStateToProps(state) {
  return { team_name: state.team_name }
}

function mapDispatchToProps(dispatch) {
  return {
    postTeamName: (team_name) => dispatch(actions.postTeamName(team_name)),
    inputTeamName: (team_name) => dispatch(actions.inputTeamName(team_name)),
    invalid: (messages) => dispatch(actions.invalid(messages)),
    valid: () => dispatch(actions.valid()) }
}

export default connect(mapStateToProps, mapDispatchToProps)(TeamNameComponent)
