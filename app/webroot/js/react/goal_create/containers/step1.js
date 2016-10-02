import {connect} from "react-redux";
import Step1Component from "../components/step1";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch))
}

export default connect(mapStateToProps, mapDispatchToProps)(Step1Component)

