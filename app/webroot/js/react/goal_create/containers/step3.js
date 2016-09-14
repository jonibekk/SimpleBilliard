import {connect} from "react-redux";
import Step3Component from "../components/step3";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(Step3Component)
