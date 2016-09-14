import React from "react";
import {browserHistory} from "react-router";
import AutoSuggest from 'react-autosuggest';
import * as Page from "../constants/Page";
import CategorySelect from "./elements/CategorySelect";
import InvalidMessageBox from "./elements/InvalidMessageBox";

export default class Step2Component extends React.Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    this.props.fetchInitialData()
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.page != Page.STEP2) {
      browserHistory.push(nextProps.goal.page)
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.validateGoal()
  }

  render() {
    const {suggestions, keyword} = this.props.goal;
    const props = {
      placeholder: "",
      value: keyword,
      onChange:this.props.onChangeAutoSuggest,
    };

    // 選択したラベル表示のHTML生成.子コンポーネント化検討
    const inputData = this.props.goal.inputData
    let inputLabels = null;
    if (inputData && inputData.labels && inputData.labels.length > 0) {
       inputLabels = inputData.labels.map((v) => {
         return <li key={v} className="goals-create-selected-labels-item"><span>{v}</span><a href="#" className="ml_8px"><i className="fa fa-times-circle" aria-hidden="true"></i></a></li>;

       });
    }

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Choose a category and set labels")}</h1>
        <p className="goals-create-description">{__("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmodtempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamcolaboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velitesse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa quiofficia deserunt mollit anim id est laborum.")}</p>
        <form className="goals-create-input" action onSubmit={(e) => this.handleSubmit(e) }>
          <CategorySelect onChange={(e) => this.props.updateInputData({category: e.target.value})} categories={this.props.goal.categories} />
          <InvalidMessageBox message={this.props.goal.validationErrors.category}/>
          <label className="goals-create-input-label">{__("Labels ?")}</label>
          <AutoSuggest
            suggestions={suggestions}
            onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
            onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
            renderSuggestion={(s) => <span>{s.name}</span>}
            getSuggestionValue={(s) => this.props.onSuggestionsFetchRequested}
            inputProps={props}
            onSuggestionSelected={this.props.onSuggestionSelected}
          />
          <InvalidMessageBox message={this.props.goal.validationErrors.labels}/>
          <ul className="goals-create-selected-labels">
            {inputLabels}
          </ul>
          <button type="submit" className="goals-create-btn-next btn">{__("Next →")}</button>
          <a className="goals-create-btn-cancel btn" href="/goals/create/step1/gucchi">{__("Back")}</a>
        </form>
      </section>
    )
  }
}
