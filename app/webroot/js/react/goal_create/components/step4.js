import React from "react";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import InvalidMessageBox from "./elements/InvalidMessageBox";

export default class Step4Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      showMoreOption: false
    }
    this.handleChange = this.handleChange.bind(this)
    this.handleClick = this.handleClick.bind(this)
  }
  componentWillMount() {
    this.props.fetchInitialData(Page.STEP4)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      // 登録完了後はTOPにリダイレクト
      location.href = "/"
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    // ゴール・tKR登録
    this.props.saveGoal()
  }

  handleChange(e) {
    console.log("handleChange")
    this.props.updateInputData({[e.target.name]: e.target.value}, "key_result")
  }

  handleClick(e) {
    e.preventDefault()
    this.setState({showMoreOption: true})
  }

  render() {
    console.log({input: this.props.goal.inputData})
    const showMoreLinkClass = "goals-create-view-more " + (this.state.showMoreOption ? "hidden" : "")

    const {units, validationErrors} = this.props.goal
    let unitOptions = [];
    for (let k in units) {
      unitOptions.push(<option key={k} value={k}>{units[k]}</option>)
    }

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set a top key result (tKR)")}</h1>
        <p className="goals-create-description">{__("Set measurable target to achieve your goal.")}</p>
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e)}>
          {/*        <label class="goals-create-input-label">{__("Key Result name?")}</label>
           <p class="goals-create-input-label-discription">{__("your top key result is required.")}</p>
           */}
          <label className="goals-create-input-label">{__("tKR name")}</label>
          <input name="name" type="text" className="form-control goals-create-input-form goals-create-input-form-tkr-name" placeholder="e.g. Increase monthly active users" onChange={this.handleChange}/>
          <label className="goals-create-input-label">{__("Unit & Range")}</label>
          <select name="value_unit" className="form-control goals-create-input-form goals-create-input-form-tkr-range-unit mod-select" onChange={this.handleChange}>
            {unitOptions}
          </select>
          <div className="goals-create-layout-flex">
            <input name="start_value" className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text" placeholder={0} onChange={this.handleChange} />
            <span className="goals-create-input-form-tkr-range-symbol">&gt;</span>
            <input name="target_value" className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text" placeholder={100} onChange={this.handleChange} />
          </div>
          <a href="#" className={showMoreLinkClass} onClick={this.handleClick}>
            <i className="fa fa-plus-circle" aria-hidden="true" />
            <span className="goals-create-interactive-link">{__("Add description")}</span>
          </a>
          <div className={this.state.showMoreOption ? "" : "hidden"}>
            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea name="description" className="form-control goals-create-input-form mod-textarea" onChange={this.handleChange} />
          </div>
          <button type="submit" className="goals-create-btn-next btn" >{__("Save and share")}</button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP3}>{__("Back")}</Link>
        </form>
      </section>

    )
  }
}
