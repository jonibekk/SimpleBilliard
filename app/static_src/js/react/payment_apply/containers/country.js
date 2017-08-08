import {connect} from "react-redux";
import CountryComponent from "../components/Country";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    movePagePaymentType: (payment_type) => dispatch(actions.movePagePaymentType(payment_type)),
  })
}

export default connect(mapStateToProps, mapDispatchToProps)(CountryComponent)
