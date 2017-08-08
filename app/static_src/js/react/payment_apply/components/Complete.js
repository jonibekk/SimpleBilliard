/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";

export default class Complete extends Base {
  constructor(props) {
    super(props);
    this.state = {
      showMoreOption: false
    }
    this.handleChange = this.handleChange.bind(this)
    this.handleClick = this.handleClick.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.COMPLETE)
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.payment.redirect_to_home) {
      super.removeBeforeUnloadHandler.apply(this)
      document.location.href = "/"
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this)
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
    const showMoreLinkClass = "goals-create-view-more " + (this.state.showMoreOption ? "hidden" : "")

    const {input_data, units, validation_errors, is_disabled_submit} = this.props.payment
    const tkrValidationErrors = validation_errors.key_result ? validation_errors.key_result : {};

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set Top Key Result")}</h1>
        <p className="goals-create-description">{__("Create a clear and most important Key Result for your goal.")}</p>
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e)}>

          <label className="goals-create-input-label">{__("Top Key Result")}</label>
          <input name="name" type="text" value={input_data.key_result.name}
                 className="form-control goals-create-input-form goals-create-input-form-tkr-name"
                 placeholder={__("eg. Increase Goalous weekly active users")}
                 maxLength={MaxLength.Name}
                 onChange={this.handleChange}/>

          <InvalidMessageBox message={tkrValidationErrors.name}/>


          <div className="goals-create-layout-flex">
            <UnitSelect value={input_data.key_result.value_unit} units={this.props.payment.units}
                        onChange={(e) => this.handleChange(e)} />
            <ValueStartEndInput input_data={input_data.key_result} onChange={(e) => this.handleChange(e)} />
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
                      value={input_data.key_result.description}
                      className="form-control goals-create-input-form mod-textarea"
                      placeholder={__("Optional")}
                      maxLength={MaxLength.Description}
                      onChange={this.handleChange}
            />
            <InvalidMessageBox message={tkrValidationErrors.description}/>
          </div>
          <button type="submit" className="goals-create-btn-next btn"
                  disabled={`${is_disabled_submit ? "disabled" : ""}`}>{__("Save and share")}</button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_CREDIT_CARD}>{__("Back")}</Link>
        </form>
      </section>
    )
  }
}
