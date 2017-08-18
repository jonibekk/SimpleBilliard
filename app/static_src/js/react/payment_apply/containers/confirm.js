import {connect} from "react-redux";
import ConfirmComponent from "../components/Confirm";
import * as actions from "../actions/index";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    savePaymentInvoice: () => dispatch(actions.savePaymentInvoice()),
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(ConfirmComponent)
