import {connect} from "react-redux";
import CampaignComponent from "../components/Campaign";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return common.getCommonDispatchToProps(dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(CampaignComponent)
