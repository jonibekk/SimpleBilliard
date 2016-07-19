import { connect } from 'react-redux'
import * as actions from '../../actions/team_name_actions'
import TeamNameComponent from '../../components/team_name'

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(TeamNameComponent)
