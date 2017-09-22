import {connect} from "react-redux";
import InputComponent from "../components/Input";
import * as actions from "../actions/index";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    updateInputData: (data) => dispatch(actions.updateInputData(data)),
    validateInvitation: () => dispatch(actions.validateInvitation()),
    fetchInputInitialData: () => dispatch(actions.fetchInputInitialData()),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(InputComponent)
