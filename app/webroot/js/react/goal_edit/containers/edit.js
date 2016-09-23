import {connect} from "react-redux";
import Edit from "../components/edit";
import * as actions from "../actions/goal_actions";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    onChangeAutoSuggest(event, {newValue}) {
      dispatch(actions.setKeyword(newValue));
    },
    onSuggestionsFetchRequested: ({value}) => dispatch(actions.onSuggestionsFetchRequested(value)),
    onSuggestionsClearRequested: () => dispatch(actions.onSuggestionsClearRequested()),
    onSuggestionSelected: (e, {suggestion}) => dispatch(actions.onSuggestionSelected(suggestion)),
    deleteLabel: (label) => dispatch(actions.deleteLabel(label)),
    addLabel: (label) => dispatch(actions.addLabel(label)),
    saveGoal: () => dispatch(actions.saveGoal()),
    validateGoal: (addData = {}) => dispatch(actions.validateGoal(addData)),
    fetchInitialData: (goalId) => dispatch(actions.fetchInitialData(goalId)),
    updateInputData: (data, key = "") => dispatch(actions.updateInputData(data, key))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Edit)

