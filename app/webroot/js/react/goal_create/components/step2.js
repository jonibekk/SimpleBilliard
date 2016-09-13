import React from "react";
import {browserHistory} from "react-router";
import AutoSuggest from 'react-autosuggest';
import * as Page from "../constants/Page";
import CategorySelect from "./elements/CategorySelect";


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
       inputLabels = inputData.labels.map(v => {
        return <div key={v}>{v}</div>;
      });
    }

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Choose a category and set labels")}</h1>
        <p className="goals-create-description">{__("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmodtempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamcolaboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velitesse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa quiofficia deserunt mollit anim id est laborum.")}</p>
        <form className="goals-create-input" action onSubmit={(e) => this.handleSubmit(e) }>
          <CategorySelect onChange={e => this.props.updateInputData({category: e.target.value})} categories={this.props.goal.categories} />
          <label className="goals-create-input-label">{__("Labels ?")}</label>
          <AutoSuggest
            suggestions={suggestions}
            onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
            onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
            renderSuggestion={s => <span>{s.name}</span>}
            getSuggestionValue={s => this.props.onSuggestionsFetchRequested}
            inputProps={props}
            onSuggestionSelected={this.props.onSuggestionSelected}
          />
          <ul>
            {inputLabels}
          </ul>

          {/*TODO:本来のhtmlは↓だが構造が違うので上手く合わせること*/}
          {/*<ul className="goals-create-input-labels-form">*/}
            {/*<li className="input-labels-form-item">*/}
              {/*<span className="input-labels-form-item-txt">{__("goalous")}</span>*/}
              {/*<a href="#" className="input-labels-form-item-choice-close" tabIndex={-1}><i*/}
                {/*className="fa fa-times-circle" aria-hidden="true"/></a>*/}
            {/*</li>*/}
            {/*/!*            <li class="input-labels-form-item">*/}
             {/*<span class="input-labels-form-item-txt">{__("web")}</span>*/}
             {/*<a href="#" class="input-labels-form-item-close" tabindex="-1"><i class="fa fa-times-circle" aria-hidden="true"></i></a>*/}
             {/*</li>*/}
             {/**!/*/}
            {/*<li className="input-labels-form-input">*/}
              {/*<input className="input-labels-form-input-text" type="text"/>*/}
            {/*</li>*/}
          {/*</ul>*/}

          <button type="submit" className="goals-create-btn-next btn">{__("Next →")}</button>
          <a className="goals-create-btn-cancel btn" href="/goals/create/step1/gucchi">{__("Back")}</a>
        </form>
      </section>
    )
  }
}
