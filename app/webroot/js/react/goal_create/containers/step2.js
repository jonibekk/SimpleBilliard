import {connect} from "react-redux";
import Step2Component from "../components/step2";
import * as actions from "../actions/goal_actions";
import * as common from "./common";

function mapStateToProps(state) {
  console.log("mapStateToProps")
  console.log({state})
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
    setKeyword: ({value}) => dispatch(actions.setKeyword(value)),
    onChangeAutoSuggest(event, {newValue}) {
      dispatch(actions.setKeyword(newValue));
    },
    onSuggestionsFetchRequested: ({value}) => dispatch(actions.onSuggestionsFetchRequested(value)),
    onSuggestionsClearRequested: () => dispatch(actions.onSuggestionsClearRequested()),
    onSuggestionSelected: (e, {suggestion}) => dispatch(actions.onSuggestionSelected(suggestion)),
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(Step2Component)
