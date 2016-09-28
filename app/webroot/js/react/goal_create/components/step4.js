import React from "react";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import ValueStartEndInput from "~/common/components/goal/ValueStartEndInput";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import {KeyResult} from "~/common/constants/Model";

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
    console.log({toNextPage:nextProps.goal.toNextPage})
    // TODO:一時的にコメントアウト。後で外す
    if (nextProps.goal.toNextPage) {
    //   // 登録完了後はTOPにリダイレクト
    //   location.href = "/"
      console.log("redirect top!!!!!!")
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    // ゴール・tKR登録
    this.props.saveGoal()
  }

  handleChange(e) {
    this.props.updateInputData({[e.target.name]: e.target.value}, "key_result")
  }

  handleClick(e) {
    e.preventDefault()
    this.setState({showMoreOption: true})
  }

  render() {
    console.log("■step4 render")
    console.log({toNextPage:this.props.goal.toNextPage})

    const showMoreLinkClass = "goals-create-view-more " + (this.state.showMoreOption ? "hidden" : "")

    const {inputData, units, validationErrors} = this.props.goal
    let unitOptions = null;
    if (units.length > 0) {
      unitOptions = units.map((v) => {
        return <option key={v.id} value={v.id}>{v.label}</option>
      })
    }

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set Top Key Result")}</h1>
        <p className="goals-create-description">{__("Create a clear and most important Key Result for your goal.")}</p>
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e)}>

          <label className="goals-create-input-label">{__("Top Key Result")}</label>
          <input name="name" type="text" value={inputData.key_result.name} className="form-control goals-create-input-form goals-create-input-form-tkr-name" placeholder={__("eg. Increase Goalous weekly active users")} onChange={this.handleChange}/>

          <InvalidMessageBox message={validationErrors.key_result.name}/>

          {/*<label className="goals-create-input-label">{__("Unit & Range")}</label>*/}
          <select name="value_unit" value={inputData.key_result.value_unit} className="form-control goals-create-input-form goals-create-input-form-tkr-range-unit mod-select" onChange={this.handleChange}>
            {unitOptions}
          </select>
          <InvalidMessageBox message={validationErrors.key_result.value_unit}/>

          <ValueStartEndInput inputData={inputData.key_result} validationErrors={validationErrors.key_result} onChange={(e) => this.handleChange(e)}/>

          <a href="#" className={showMoreLinkClass} onClick={this.handleClick}>
            <i className="fa fa-plus-circle" aria-hidden="true" />
            <span className="goals-create-interactive-link">{__("Add description")}</span>
          </a>
          <div className={this.state.showMoreOption ? "" : "hidden"}>
            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea name="description" value={inputData.key_result.description} className="form-control goals-create-input-form mod-textarea" onChange={this.handleChange} />
            <InvalidMessageBox message={validationErrors.key_result.description}/>
          </div>
          <button type="submit" className="goals-create-btn-next btn" >{__("Save and share")}</button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP3}>{__("Back")}</Link>
        </form>
      </section>

    )
  }
}
