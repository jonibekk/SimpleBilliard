import {connect} from "react-redux";
import UpgradePlanComponent from "../components/UpgradePlan";
import * as actions from "../actions/index";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    selectPricePlan: (data) => dispatch(actions.selectPricePlan(data)),
    upgradePricePlan: (plan_code) => dispatch(actions.upgradePricePlan(plan_code)),
    fetchInitialData: () => dispatch(actions.fetchInitialData()),
    resetStates: () => dispatch(actions.resetStates()),

  };
}

export default connect(mapStateToProps, mapDispatchToProps)(UpgradePlanComponent)
