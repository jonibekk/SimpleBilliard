import {connect} from "react-redux";
import CompanyComponent from "../components/Company";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return common.getCommonDispatchToProps(dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(CompanyComponent)
