import { connect } from 'react-redux'
import ListComponent from '../components/list'
import * as actions from '../actions/list_actions'

function mapStateToProps(state) {
  return { list: state.list }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchCollaborators: (is_initialize = false) => { dispatch(actions.fetchCollaborators(is_initialize)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ListComponent)
