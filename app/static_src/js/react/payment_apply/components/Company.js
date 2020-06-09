// TODO.Payment: Translate untranslated sentence
/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import {PaymentSetting} from "~/common/constants/Model";
import FormTextBox from "~/payment_apply/components/elements/FormTextBox";
import RowMultipleTextBoxes from "~/payment_apply/components/elements/RowMultipleTextBoxes";

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
      const {type} = nextProps.payment.input_data.payment_setting
      if (type == PaymentSetting.PAYMENT_TYPE.CREDIT_CARD) {
        browserHistory.push(Page.URL_CREDIT_CARD)
      } else {
        browserHistory.push(Page.URL_INVOICE)
      }
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
    const {input_data, validation_errors, is_disabled_submit, is_campaign_team} = this.props.payment;
    const {payment_setting} = input_data
    const errors_payment_setting = validation_errors.payment_setting ? validation_errors.payment_setting : {};

    let attributes = [
      {
        id: "PaymentsContactPersonFirstName",
        name: "contact_person_first_name",
        label: __("First Name "),
        value: payment_setting.contact_person_first_name,
        placeholder: __("eg. Bruce"),
        max_length: 128,
        err_msg: errors_payment_setting.contact_person_first_name,
      },
      {
        id: "PaymentsContactPersonLastName",
        name: "contact_person_last_name",
        label: __("Last Name "),
        value: payment_setting.contact_person_last_name,
        placeholder: __("eg. Jobs"),
        max_length: 128,
        err_msg: errors_payment_setting.contact_person_last_name,
      }
    ];
    if (payment_setting.company_country == 'JP') {
      attributes.reverse();
    }

    return (
      <section className="panel payment">
        <form
          className="form-horizontal company-info" name="addCompanyInfo"
          id="PaymentsAddCompanyInfoForm" acceptCharset="utf-8"
          onSubmit={(e) => this.onSubmit(e)}
        >
        <div className="panel-container">
        <h3>{__("Enter Company Information")}</h3>
          <FormTextBox
            id="PaymentsCompanyName"
            name="company_name"
            value={payment_setting.company_name}
            label={__("Company Name")}
            placeholder={__("Colorkrew Inc.")}
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
              placeholder={__("12345 ")}
              err_msg={errors_payment_setting.company_post_code}
              max_length={16}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            <FormTextBox
              id="PaymentsCompanyAddressRegion"
              name="company_region"
              value={payment_setting.company_region}
              label={__("State/Province/Region")}
              placeholder={__("California")}
              err_msg={errors_payment_setting.company_region}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            <FormTextBox
              id="PaymentsCompanyAddressCity"
              name="company_city"
              value={payment_setting.company_city}
              label={__("City")}
              placeholder={__("Los Angeles")}
              err_msg={errors_payment_setting.company_city}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            <FormTextBox
              id="PaymentsCompanyAddressStreet"
              name="company_street"
              value={payment_setting.company_street}
              label={__("Street")}
              placeholder={__("1234 Street Name")}
              err_msg={errors_payment_setting.company_street}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
          </fieldset>
          <fieldset className="company-info-fieldset">
            <legend className="company-info-legend">{__("Company Contact")}</legend>
            <RowMultipleTextBoxes
              attributes={attributes}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            {payment_setting.type == PaymentSetting.PAYMENT_TYPE.INVOICE &&
            <RowMultipleTextBoxes
              attributes={[
                {
                  id: "PaymentsContactPersonLastNameKana",
                  name: "contact_person_last_name_kana",
                  label: __("Last Name Kana"),
                  value: payment_setting.contact_person_last_name_kana,
                  placeholder: "スズキ",
                  max_length: 128,
                  err_msg: errors_payment_setting.contact_person_last_name_kana,
                },
                {
                  id: "PaymentsContactPersonFirstNameKana",
                  name: "contact_person_first_name_kana",
                  label: __("First Name Kana"),
                  value: payment_setting.contact_person_first_name_kana,
                  placeholder: "タロウ",
                  max_length: 128,
                  err_msg: errors_payment_setting.contact_person_first_name_kana
                }
              ]}
              onChange={(e) => this.onChange(e, "payment_setting")}
            />
            }
          </fieldset>
          <FormTextBox
            id="PaymentsContactPersonEmail"
            type="email"
            name="contact_person_email"
            value={payment_setting.contact_person_email}
            label={__("Email Address")}
            placeholder={__("name@company.com")}
            err_msg={errors_payment_setting.contact_person_email}
            onChange={(e) => this.onChange(e, "payment_setting")}
          />
          <FormTextBox
            id="PaymentsContactPersonPhone"
            type="tel"
            name="contact_person_tel"
            value={payment_setting.contact_person_tel}
            label={__("Telephone")}
            placeholder="00000000000"
            err_msg={errors_payment_setting.contact_person_tel}
            onChange={(e) => this.onChange(e, "payment_setting")}
            max_length={20}
          />
          </div>
          <div className="panel-footer setting_pannel-footer">
            <Link to={ is_campaign_team ? '/payments/apply/campaign' : '/payments/apply' }
                  className="btn btn-link design-cancel bd-radius_4px">
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
