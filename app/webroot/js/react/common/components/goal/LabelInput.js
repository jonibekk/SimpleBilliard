import React from "react";
import AutoSuggest from "react-autosuggest";

export default class LabelInput extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    const props = {
      placeholder: __("You can save maximum 5 labels"),
      value: this.props.keyword,
      onChange: this.props.onChange,
      onKeyDown: this.props.onKeyDown,
      onKeyPress: this.props.onKeyPress
    };

    return (
      <div>
        <label className="goals-create-input-label">{__("Labels")}</label>
        <AutoSuggest
          suggestions={this.props.suggestions}
          onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
          onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
          renderSuggestion={this.props.renderSuggestion}
          getSuggestionValue={this.props.getSuggestionValue}
          inputProps={props}
          onSuggestionSelected={this.props.onSuggestionSelected}
          shouldRenderSuggestions={this.props.shouldRenderSuggestions}
        />
        <ul className="goals-create-selected-labels">
          {
            this.props.inputLabels.map((v, i) => {
              return (
                <li key={i} className="goals-create-selected-labels-item">
                  <span>{v}</span>
                  <a href="#" className="ml_8px" onClick={this.props.onDeleteLabel} data-label={v}>
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
  inputLabels: React.PropTypes.array,
};
LabelInput.defaultProps = {
  suggestions: [],
  keyword: "",
  inputLabels: [],
};
