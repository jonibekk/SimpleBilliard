import { connect } from 'react-redux'
import { postTeamName, inputTeamName } from '../actions/team_name_actions'
import TeamNameComponent from '../components/team_name'

function mapStateToProps(state) {
  return { team_name: state.team_name }
}

function mapDispatchToProps(dispatch) {
  return {
    postTeamName: (team_name) => dispatch(postTeamName(team_name)),
    inputTeamName: (team_name) => dispatch(inputTeamName(team_name))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(TeamNameComponent)
