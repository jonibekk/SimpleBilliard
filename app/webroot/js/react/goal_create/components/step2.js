import React from "react";
import {browserHistory, Link} from "react-router";
import AutoSuggest from "react-autosuggest";
import * as Page from "../constants/Page";
import CategorySelect from "./elements/CategorySelect";
import InvalidMessageBox from "./elements/InvalidMessageBox";
import * as KeyCode from "../constants/KeyCode";
import LabelInput from "./elements/LabelInput";

export default class Step2Component extends React.Component {
  constructor(props) {
    super(props);
    this.deleteLabel = this.deleteLabel.bind(this)
    this.addLabel = this.addLabel.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.STEP2)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      browserHistory.push(Page.URL_STEP3)
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    if (e.keyCode == KeyCode.ENTER) {
      return false
    }
    this.props.validateGoal(Page.STEP2)
  }

  deleteLabel(e) {
    const label = e.currentTarget.getAttribute("data-label")
    this.props.deleteLabel(label)
  }

  addLabel(e) {
    // Enterキーを押した時にラベルとして追加
    if (e.keyCode == KeyCode.ENTER) {
      this.props.addLabel(e.target.value)
    }
  }

  onKeyPress(e) {
    // ラベル入力でEnterキーを押した場合submitさせない
    // e.keyCodeはonKeyPressイベントでは取れないのでe.charCodeを使用
    if (e.charCode == KeyCode.ENTER) {
      e.preventDefault()
      return false
    }
  }

  render() {
    let {suggestions, keyword, inputData, validationErrors} = this.props.goal;

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Choose a category and set labels")}</h1>
        <p
          className="goals-create-description">{__("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmodtempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamcolaboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velitesse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa quiofficia deserunt mollit anim id est laborum.")}</p>
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e) }>
          <CategorySelect
            onChange={(e) => this.props.updateInputData({goal_category_id: e.target.value})}
            categories={this.props.goal.categories}
            value={inputData.goal_category_id}/>
          <InvalidMessageBox message={validationErrors.goal_category_id}/>

          <LabelInput
            suggestions={suggestions}
            keyword={keyword}
            inputLabels={inputData.labels}
            onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
            onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
            renderSuggestion={(s) => <span>{s.name}</span>}
            getSuggestionValue={(s) => this.props.onSuggestionsFetchRequested}
            onChange={this.props.onChangeAutoSuggest}
            onSuggestionSelected={this.props.onSuggestionSelected}
            shouldRenderSuggestions={() => true}
            onDeleteLabel={this.deleteLabel}
            onKeyDown={this.addLabel}
            onKeyPress={this.onKeyPress}
          />
          <InvalidMessageBox message={validationErrors.labels}/>

          <button type="submit" className="goals-create-btn-next btn">{__("Next →")}</button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP1}>{__("Back")}</Link>
        </form>
      </section>
    )
  }
}
