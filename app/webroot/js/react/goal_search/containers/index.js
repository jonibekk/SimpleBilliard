import {connect} from "react-redux";
import Goals from "~/goal_search/components/Goals";
import * as actions from "~/goal_search/actions/goal_actions";
import * as LabelInput from "~/goal_search/modules/LabelInput";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  // return Object.assign({}, LabelInput.getDispatchToProps(dispatch), {
  return {
      fetchInitialData: () => dispatch(actions.fetchInitialData()),
      updateData: (data, key = "") => dispatch(actions.updateData(data, key = "")),
      updateFilter: (data) => dispatch(actions.updateFilter(data)),
      fetchMoreGoals: (url) => dispatch(actions.fetchMoreGoals(url))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Goals)

