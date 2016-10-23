import React from "react";
import { connect } from 'react-redux';
import * as Module from "~/goal_search/modules/LabelInput";
import AutoSuggest from "react-autosuggest";
import * as KeyCode from "~/common/constants/KeyCode";

class LabelInput extends React.Component {

  deleteLabel(e) {

    e.preventDefault()
    const label = e.currentTarget.getAttribute("data-label")
    console.log("■deleteLabel")
    console.log({label})
    this.props.dispatch(Module.deleteLabel(label));
  }

  addLabel(e) {
    // Enterキーを押した時にラベルとして追加
    if (e.keyCode == KeyCode.ENTER) {
      this.props.dispatch(Module.addLabel(e.target.value));
    }
  }

  onChangeLabelInput(event, {newValue}) {
    if (!newValue) {
      return
    }
    this.props.dispatch(Module.setKeyword(newValue));
  }

  render() {
    const props = {
      maxLength: 20,
      value: this.props.keyword,
      onChange: this.onChangeLabelInput.bind(this),
      onKeyDown: this.addLabel.bind(this),
      // onKeyPress: this.onKeyPress
      className:"form-control gl",
      disabled:this.props.input_labels.length == 3
    };
    const {dispatch} = this.props

    return (
      <div>
        <label className="gl-form-label" htmlFor>ラベル</label>
        <p className="gl-form-guide">
          Enterを押すと追加されます。(最大3個まで検索可能)
        </p>
        <AutoSuggest
          suggestions={this.props.suggestions}
          onSuggestionsFetchRequested={({value}) => dispatch(Module.onSuggestionsFetchRequested(value))}
          onSuggestionsClearRequested={() => dispatch(Module.onSuggestionsClearRequested())}
          renderSuggestion={(s) => <span>{s.name}</span>}
          getSuggestionValue={({value}) => dispatch(Module.onSuggestionsFetchRequested(value))}
          inputProps={props}
          onSuggestionSelected={(e, {suggestion}) => dispatch(Module.onSuggestionSelected(suggestion))}
          shouldRenderSuggestions={() => true}
        />
        <ul className="goals-create-selected-labels">
          {
            this.props.input_labels.map((v, i) => {
              return (
                <li key={i} className="goals-create-selected-labels-item">
                  <span>{v}</span>
                  <a href="#" className="ml_8px" onClick={this.deleteLabel.bind(this)} data-label={v}>
                    <i className="fa fa-times-circle" aria-hidden="true"></i>
                  </a>
                </li>
              )
            })
          }
        </ul>
      </div>
    )
  }
}
LabelInput.propTypes = {
  suggestions: React.PropTypes.array,
  keyword: React.PropTypes.any,
  input_labels: React.PropTypes.array,
};
LabelInput.defaultProps = {
  suggestions: [],
  keyword: "",
  input_labels: [],
};

export default connect()(LabelInput);
