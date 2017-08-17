import {connect} from "react-redux";
import ConfirmComponent from "../components/Confirm";
import * as actions from "../actions/index";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    resetBilling: () => dispatch(actions.resetBilling()),
    setBillingSameAsCompany: () => dispatch(actions.setBillingSameAsCompany()),
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(ConfirmComponent)
