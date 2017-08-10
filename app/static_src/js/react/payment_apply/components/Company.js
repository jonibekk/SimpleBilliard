// TODO.Payment: Translate untranslated sentence

/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import FormTextBox from "~/payment_apply/components/elements/FormTextBox";

export default class Company extends Base {
  constructor(props) {
    super(props);
    this.onChange = this.onChange.bind(this)
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.payment.to_next_page) {
      browserHistory.push(Page.URL_CREDIT_CARD)
    }
  }

  componentWillUnmount() {
    this.props.resetStates();
    super.componentWillUnmount.apply(this)
  }

  onSubmit(e) {
    e.preventDefault()
    this.props.validatePayment(Page.COMPANY)
  }

  onChange(e) {
    let data = {}
    data[e.target.name] = e.target.value;
    this.props.updateInputData(data, 'payment_setting')
  }

  render() {
    const {input_data, validation_errors} = this.props.payment;
    const {payment_setting} = input_data

    return (
      <section className="panel company-info">
        <h3>{__("Enter Company Information")}</h3>
        <form
          className="form-horizontal" name="addCompanyInfo"
          id="PaymentsAddCompanyInfoForm" acceptCharset="utf-8"
          onSubmit={(e) => this.onSubmit(e)}
        >

          <FormTextBox
            id="PaymentsCompanyName"
            name="company_name"
            value={payment_setting.company_name}
            label={__("Company Name")}
            placeholder="株式会社ISAO"
            onChange={this.onChange}
          />
          <fieldset className="company-info-fieldset">
            <legend className="company-info-legend">{__("Company Address")}</legend>
            <FormTextBox
              id="PaymentsCompanyPostCode"
              name="company_post_code"
              type="tel"
              label={__("Post Code")}
              placeholder="000-0000"
              max_length={16}
              onChange={this.onChange}
            />
            <FormTextBox
              id="PaymentsCompanyAddressRegion"
              name="company_region"
              label={__("Region")}
              placeholder="東京都"
              onChange={this.onChange}
            />
            <FormTextBox
              id="PaymentsCompanyAddressCity"
              name="company_address_city"
              label={__("City")}
              placeholder="台東区"
              onChange={this.onChange}
            />
            <FormTextBox
              id="PaymentsCompanyAddressStreet"
              name="company_street"
              label={__("Address")}
              placeholder="台東１－１－１"
              onChange={this.onChange}
            />
          </fieldset>
          <fieldset className="company-info-fieldset">
            <legend className="company-info-legend">Company Contact</legend>
            <FormTextBox
              id="PaymentsContactNameLast"
              name="contact_last_name"
              label={__("LastName")}
              placeholder="東京"
              onChange={this.onChange}
            />
            <FormTextBox
              id="PaymentsContactNameFirst"
              name="contact_first_name"
              label={__("FirstName")}
              placeholder="太郎"
              onChange={this.onChange}
            />
          </fieldset>
          <FormTextBox
            id="PaymentsContactEmail"
            name="contact_first_name"
            label={__("Email")}
            type="email"
            placeholder="name@company.com"
            onChange={this.onChange}
          />
          <FormTextBox
            id="PaymentsContactPhone"
            name="contact_first_name"
            type="tel"
            label={__("Telephone")}
            placeholder="000-0000-0000"
            onChange={this.onChange}
          />
          <div className="panel-footer setting_pannel-footer">
            <Link to="/payments/apply" className="btn btn-link design-cancel bd-radius_4px">
              {__("Back")}</Link>
            <input type="submit" className="btn btn-primary" defaultValue={__("Next")}/>
          </div>
        </form>
      </section>
    )
  }
}
