// TODO.Payment: Translate untranslated sentence
/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import FormTextBox from "~/payment_apply/components/elements/FormTextBox";
import RowMultipleTextBoxes from "~/payment_apply/components/elements/RowMultipleTextBoxes";

export default class Invoice extends Base {
  constructor(props) {
    super(props);
    this.onChange = this.onChange.bind(this)
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.payment.to_next_page) {
      browserHistory.push(Page.URL_CONFIRM)
    }
  }

  componentWillUnmount() {
    this.props.resetStates();
    super.componentWillUnmount.apply(this)
  }

  onSubmit(e) {
    e.preventDefault()
    this.props.validatePayment(Page.INVOICE)
  }

  onChange(e, parent_key) {
    let data = {}
    data[e.target.name] = e.target.value;
    this.props.updateInputData(data, parent_key)
  }

  onCheck(e) {
    if (e.target.checked) {
      this.props.setBillingSameAsCompany()
    } else {
      this.props.resetBilling()
    }
  }

  render() {
    const {input_data, validation_errors, is_disabled_submit, is_same_as_company_info} = this.props.payment;
    const {invoice} = input_data
    const errors_invoice = validation_errors.invoice ? validation_errors.invoice : {};

    return (
      <section className="panel payment company-info">
          <form
            className="form-horizontal" name="addInvoiceInfo"
            id="PaymentsAddInvoiceInfoForm" acceptCharset="utf-8"
            onSubmit={(e) => this.onSubmit(e)}
          >
        <div className="panel-container">
          <h3>{__("Enter Billing Information")}</h3>
            <div className="form-group">
              <label htmlFor="checkSameAsCompanyInfo">
                <input
                  type="checkbox" id="checkSameAsCompanyInfo"
                  onChange={this.onCheck.bind(this)}
                  checked={is_same_as_company_info}
                />{__("Same as company information")}
              </label>
            </div>
            <FormTextBox
              id="PaymentsBillingCompanyName"
              name="company_name"
              value={invoice.company_name}
              label={__("Company Name")}
              placeholder={__("Colorkrew Inc.")}
              disabled={is_same_as_company_info}
              err_msg={errors_invoice.company_name}
              onChange={(e) => this.onChange(e, "invoice")}
            />
            <fieldset className="company-info-fieldset">
              <legend className="company-info-legend">{__("Company Address")}</legend>
              <FormTextBox
                id="PaymentsBillingCompanyPostCode"
                type="tel"
                name="company_post_code"
                value={invoice.company_post_code}
                label={__("Post Code")}
                placeholder={__("12345 ")}
                disabled={is_same_as_company_info}
                err_msg={errors_invoice.company_post_code}
                max_length={16}
                onChange={(e) => this.onChange(e, "invoice")}
              />
              <FormTextBox
                id="PaymentsBillingCompanyAddressRegion"
                name="company_region"
                value={invoice.company_region}
                label={__("State/Province/Region")}
                placeholder={__("California")}
                disabled={is_same_as_company_info}
                err_msg={errors_invoice.company_region}
                onChange={(e) => this.onChange(e, "invoice")}
              />
              <FormTextBox
                id="PaymentsBillingCompanyAddressCity"
                name="company_city"
                value={invoice.company_city}
                label={__("City")}
                placeholder={__("Los Angeles")}
                disabled={is_same_as_company_info}
                err_msg={errors_invoice.company_city}
                onChange={(e) => this.onChange(e, "invoice")}
              />
              <FormTextBox
                id="PaymentsBillingCompanyAddressStreet"
                name="company_street"
                value={invoice.company_street}
                label={__("Street")}
                placeholder={__("1234 Street Name")}
                disabled={is_same_as_company_info}
                err_msg={errors_invoice.company_street}
                onChange={(e) => this.onChange(e, "invoice")}
              />
            </fieldset>
            <fieldset className="company-info-fieldset">
              <legend className="company-info-legend">{__("Company Contact")}</legend>
              <RowMultipleTextBoxes
                attributes={[
                  {
                    id: "PaymentsContactPersonLastName",
                    name: "contact_person_last_name",
                    label: __("Last Name "),
                    value: invoice.contact_person_last_name,
                    placeholder: __("eg. Jobs"),
                    max_length: 128,
                    disabled:is_same_as_company_info,
                    err_msg: errors_invoice.contact_person_last_name,
                  },
                  {
                    id: "PaymentsContactPersonFirstName",
                    name: "contact_person_first_name",
                    label: __("First Name "),
                    value: invoice.contact_person_first_name,
                    placeholder: __("eg. Bruce"),
                    max_length: 128,
                    disabled:is_same_as_company_info,
                    err_msg: errors_invoice.contact_person_first_name,
                  }
                ]}
                onChange={(e) => this.onChange(e, "invoice")}
              />
              <RowMultipleTextBoxes
                attributes={[
                  {
                    id: "PaymentsContactPersonLastNameKana",
                    name: "contact_person_last_name_kana",
                    label: __("Last Name Kana"),
                    value: invoice.contact_person_last_name_kana,
                    placeholder: "スズキ",
                    max_length: 128,
                    disabled:is_same_as_company_info,
                    err_msg: errors_invoice.contact_person_last_name_kana,
                  },
                  {
                    id: "PaymentsContactPersonFirstNameKana",
                    name: "contact_person_first_name_kana",
                    label: __("First Name Kana"),
                    value: invoice.contact_person_first_name_kana,
                    placeholder: "タロウ",
                    max_length: 128,
                    disabled:is_same_as_company_info,
                    err_msg: errors_invoice.contact_person_first_name_kana
                  }
                ]}
                onChange={(e) => this.onChange(e, "invoice")}
              />
            </fieldset>
            <FormTextBox
              id="PaymentsBillingContactPersonEmail"
              type="email"
              name="contact_person_email"
              value={invoice.contact_person_email}
              label={__("Email Address")}
              placeholder={__("name@company.com")}
              disabled={is_same_as_company_info}
              err_msg={errors_invoice.contact_person_email}
              onChange={(e) => this.onChange(e, "invoice")}
            />
            <FormTextBox
              id="PaymentsBillingContactPersonPhone"
              type="tel"
              name="contact_person_tel"
              value={invoice.contact_person_tel}
              label={__("Telephone")}
              placeholder="00000000000"
              disabled={is_same_as_company_info}
              err_msg={errors_invoice.contact_person_tel}
              onChange={(e) => this.onChange(e, "invoice")}
            />
            </div>
            <div className="panel-footer setting_pannel-footer">
              <Link to="/payments/apply/company" className="btn btn-link design-cancel bd-radius_4px">
                {__("Back")}</Link>
              <button className="btn btn-primary" disabled={is_disabled_submit ? "disabled" : ""}>
                {__("Next")}
              </button>
            </div>
          </form>
      </section>
    )
  }
}
