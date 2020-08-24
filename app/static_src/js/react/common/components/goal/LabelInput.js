import React from "react";
import AutoSuggest from "react-autosuggest";

export default class LabelInput extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    const props = {
      maxLength: 20,
      value: this.props.keyword,
      onChange: this.props.onChange,
      onKeyDown: this.props.onKeyDown,
      onKeyPress: this.props.onKeyPress
    };

    return (
      <div>
        <label className="goals-create-input-label">{__("Labels")}</label>
        <p className="goals-create-description">
          {__("Add by pressing the Enter.(You can save maximum 20 labels)")}
        </p>
        <AutoSuggest
          suggestions={this.props.suggestions}
          onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
          onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
          renderSuggestion={(s) =>
            <span>
              {s.name}&nbsp;
              {s.goal_label_count ? <span className="react-autosuggest__suggestion-label-complement">({s.goal_label_count})</span> : ""}
            </span>
          }
          getSuggestionValue={this.props.getSuggestionValue}
          inputProps={props}
          onSuggestionSelected={this.props.onSuggestionSelected}
          shouldRenderSuggestions={this.props.shouldRenderSuggestions}
        />
        <ul className="selected-labels">
          {
            this.props.inputLabels.map((v, i) => {
              return (
                <li key={i} className="selected-labels-item">
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
