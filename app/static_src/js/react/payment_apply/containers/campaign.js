import {connect} from "react-redux";
import CampaignComponent from "../components/Campaign";
import * as actions from "../actions/index";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    updateSelectedCampaignPlanInfo: (data) => dispatch(actions.updateSelectedCampaignPlanInfo(data)),
  });
  return common.getCommonDispatchToProps(dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(CampaignComponent)
