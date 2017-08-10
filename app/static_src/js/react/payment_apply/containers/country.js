import {connect} from "react-redux";
import CountryComponent from "../components/Country";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
  })
}

export default connect(mapStateToProps, mapDispatchToProps)(CountryComponent)
