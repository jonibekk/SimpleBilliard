import { connect } from 'react-redux'
import ConfirmComponent from '../components/Confirm'
import * as actions from "../actions/index";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchConfirmInitialData: () => dispatch(actions.fetchConfirmInitialData()),
    saveInvitation: () => dispatch(actions.saveInvitation()),
  };
}

export default connect(mapStateToProps, mapDispatchToProps)(ConfirmComponent)
