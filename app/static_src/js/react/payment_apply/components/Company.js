// TODO.Payment: Translate untranslated sentence
/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
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

  onChange(e, parent_key) {
    let data = {}
    data[e.target.name] = e.target.value;
    this.props.updateInputData(data, parent_key)
  }

  render() {
    const {input_data, validation_errors} = this.props.payment;
    const {payment_setting} = input_data
    const errors_payment_setting = validation_errors.payment_setting ? validation_errors.payment_setting : {};

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
            err_msg={errors_payment_setting.company_name}
            onChange={(e) => this.onChange(e, "payment_setting")}
          />
          <fieldset className="company-info-fieldset">
            <legend className="company-info-legend">{__("Company Address")}</legend>
            <FormTextBox
              id="PaymentsCompanyPostCode"
              type="tel"
              name="company_post_code"
              value={payment_setting.company_post_code}
              label={__("Post Code")}
              placeholder="000-0000"
              err_msg={errors_payment_setting.company_post_code}
              max_length={16}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            <FormTextBox
              id="PaymentsCompanyAddressRegion"
              name="company_region"
              value={payment_setting.company_region}
              label={__("Region")}
              placeholder="東京都"
              err_msg={errors_payment_setting.company_region}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            <FormTextBox
              id="PaymentsCompanyAddressCity"
              name="company_address_city"
              value={payment_setting.company_address_city}
              label={__("City")}
              placeholder="台東区"
              err_msg={errors_payment_setting.company_address_city}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            <FormTextBox
              id="PaymentsCompanyAddressStreet"
              name="company_street"
              value={payment_setting.company_street}
              label={__("Address")}
              placeholder="台東１－１－１"
              err_msg={errors_payment_setting.company_street}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
          </fieldset>
          <fieldset className="company-info-fieldset">
            <legend className="company-info-legend">Company Contact</legend>
            <FormTextBox
              id="PaymentsContactPersonLastName"
              name="contact_person_last_name"
              value={payment_setting.contact_person_last_name}
              label={__("LastName")}
              placeholder="東京"
              err_msg={errors_payment_setting.contact_person_last_name}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            <FormTextBox
              id="PaymentsContactPersonFirstName"
              name="contact_person_first_name"
              value={payment_setting.contact_person_first_name}
              label={__("FirstName")}
              placeholder="太郎"
              err_msg={errors_payment_setting.contact_person_first_name}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            {payment_setting.company_country == 'JP' &&
            <div>
              <FormTextBox
                id="PaymentsContactPersonLastNameKana"
                name="contact_person_last_name_kana"
                value={payment_setting.contact_person_last_name_kana}
                label={__("LastName Kana")}
                placeholder="東京"
                err_msg={errors_payment_setting.contact_person_last_name_kana}
                onChange={(e) => this.onChange(e, "payment_setting")}
              />
              <FormTextBox
                id="PaymentsContactPersonFirstNameKana"
                name="contact_person_first_name_kana"
                value={payment_setting.contact_person_first_name_kana}
                label={__("FirstName Kana")}
                placeholder="太郎"
                err_msg={errors_payment_setting.contact_person_first_name_kana}
                onChange={(e) => this.onChange(e, "payment_setting")}
              />
            </div>
            }
          </fieldset>
          <FormTextBox
            id="PaymentsContactPersonEmail"
            type="email"
            name="contact_person_email"
            value={payment_setting.contact_person_email}
            label={__("Email")}
            placeholder="name@company.com"
            err_msg={errors_payment_setting.contact_person_email}
            onChange={(e) => this.onChange(e, "payment_setting")}
          />
          <FormTextBox
            id="PaymentsContactPersonPhone"
            type="tel"
            name="contact_person_tel"
            value={payment_setting.contact_person_tel}
            label={__("Telephone")}
            placeholder="000-0000-0000"
            err_msg={errors_payment_setting.contact_person_tel}
            onChange={(e) => this.onChange(e, "payment_setting")}
          />
          <div className="panel-footer setting_pannel-footer">
            <Link to="/payments/apply" className="btn btn-link design-cancel bd-radius_4px">
              {__("Back")}</Link>
            <input type="submit" className="btn btn-primary" defaultValue={__("Next")} disabled=""/>
          </div>
        </form>
      </section>
    )
  }
}
