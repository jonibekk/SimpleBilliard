import {connect} from "react-redux";
import CreditCardComponent from "../components/CreditCard";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    savePaymentSetting: () => dispatch(actions.savePaymentSetting()),
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(CreditCardComponent)
