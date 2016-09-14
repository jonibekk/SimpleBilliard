import {connect} from "react-redux";
import Step2Component from "../components/step2";
import * as actions from "../actions/goal_actions";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    validateGoal: () => dispatch(actions.validateGoal()),
    fetchInitialData: () => dispatch(actions.fetchInitialData()),
    setKeyword: (value) => dispatch(actions.setKeyword(value)),
    onChangeAutoSuggest(event, {newValue}) {
      console.log(newValue)
      dispatch(actions.setKeyword(newValue));
    },
    onSuggestionsFetchRequested: ({value}) => dispatch(actions.onSuggestionsFetchRequested(value)),
    onSuggestionsClearRequested: () => dispatch(actions.onSuggestionsClearRequested()),
    onSuggestionSelected: (e, {suggestion}) => dispatch(actions.onSuggestionSelected(suggestion)),
    updateInputData: (data) => dispatch(actions.updateInputData(data)),
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Step2Component)
