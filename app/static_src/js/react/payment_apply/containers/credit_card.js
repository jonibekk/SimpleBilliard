import {connect} from "react-redux";
import CreditCardComponent from "../components/CreditCard";
import * as actions from "../actions/index";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    savePaymentCc: (card, extra_details = {}) => dispatch(actions.savePaymentCc(card, extra_details)),
    initStripe: (stripe) => dispatch(actions.initStripe(stripe)),
    disableSubmit: () => dispatch(actions.disableSubmit()),
    enableSubmit: () => dispatch(actions.enableSubmit()),
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(CreditCardComponent)
