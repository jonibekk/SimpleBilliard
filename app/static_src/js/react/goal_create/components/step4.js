/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import ValueStartEndInput from "~/common/components/goal/ValueStartEndInput";
import UnitSelect from "~/common/components/goal/UnitSelect";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import {MaxLength} from "~/common/constants/App";
import Base from "~/common/components/Base";

export default class Step4Component extends Base {
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

  componentDidMount() { super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      browserHistory.push(Page.URL_STEP5)
    }
    if (nextProps.goal.redirect_to_home) {
      super.removeBeforeUnloadHandler.apply(this)
      document.location.href = "/"
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this)
  }

  handleSubmit(e) {
    e.preventDefault()
    const {saveGoal, validateGoal, goal} = this.props

    if (goal.groups_enabled) {
      validateGoal(Page.STEP4)
    } else {
      saveGoal()
    }
  }

  handleChange(e) {
    this.props.updateInputData({[e.target.name]: e.target.value}, "key_result")
  }

  handleClick(e) {
    e.preventDefault()
    this.setState({showMoreOption: true})
  }

  render() {
    const showMoreLinkClass = "goals-create-view-more " + (this.state.showMoreOption ? "hidden" : "")

    const {inputData, units, validationErrors, isDisabledSubmit, groups_enabled} = this.props.goal
    const tkrValidationErrors = validationErrors.key_result ? validationErrors.key_result : {};

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set Top Key Result")}</h1>
        <p className="goals-create-description">{__("Create a clear and most important Key Result for your goal.")}</p>
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e)}>

          <label className="goals-create-input-label">{__("Top Key Result")}</label>
          <input name="name" type="text" value={inputData.key_result.name}
                 className="form-control goals-create-input-form goals-create-input-form-tkr-name"
                 placeholder={__("eg. Increase Goalous weekly active users")}
                 maxLength={MaxLength.Name}
                 onChange={this.handleChange}/>

          <InvalidMessageBox message={tkrValidationErrors.name}/>


          <div className="goals-create-layout-flex">
            <UnitSelect value={inputData.key_result.value_unit} units={this.props.goal.units}
                        onChange={(e) => this.handleChange(e)} />
            <ValueStartEndInput inputData={inputData.key_result} onChange={(e) => this.handleChange(e)} />
          </div>
          <InvalidMessageBox message={tkrValidationErrors.value_unit}/>
          <InvalidMessageBox message={tkrValidationErrors.start_value}/>
          <InvalidMessageBox message={tkrValidationErrors.target_value}/>

          <a href="#" className={showMoreLinkClass} onClick={this.handleClick}>
            <i className="fa fa-plus-circle" aria-hidden="true"/>
            <span className="goals-create-interactive-link">{__("Add description")}</span>
          </a>
          <div className={this.state.showMoreOption ? "" : "hidden"}>
            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea name="description"
                      value={inputData.key_result.description}
                      className="form-control goals-create-input-form mod-textarea"
                      placeholder={__("Optional")}
                      maxLength={MaxLength.Description}
                      onChange={this.handleChange}
            />
            <InvalidMessageBox message={tkrValidationErrors.description}/>
          </div>
          {
            groups_enabled 
            ?
            <button type="submit" className="goals-create-btn-next btn">{__("Next →")}</button>
            :
            <button type="submit" className="goals-create-btn-next btn" >
              {__("Save and share")}
            </button>
          }
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP3}>{__("Back")}</Link>
        </form>
      </section>
    )
  }
}
