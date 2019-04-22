import {connect} from "react-redux";
import Goals from "~/goal_search/components/Goals";
import * as actions from "~/goal_search/actions/goal_actions";
import * as LabelInput from "~/goal_search/modules/LabelInput";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitialData: () => dispatch(actions.fetchInitialData()),
    updateData: (data, key = "") => dispatch(actions.updateData(data, key = "")),
    updateFilter: (data) => dispatch(actions.updateFilter(data)),
    updateKeyword: (data) => dispatch(actions.updateKeyword(data)),
    fetchMoreGoals: (url) => dispatch(actions.fetchMoreGoals(url)),
    downloadCsv: (data) => dispatch(actions.downloadCsv(data)),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Goals)

