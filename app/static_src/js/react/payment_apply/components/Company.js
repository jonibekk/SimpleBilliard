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
  }

  componentWillMount() {
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
    super.componentWillUnmount.apply(this)
  }

  onSubmit(e) {
    e.preventDefault()
    this.props.validatePayment(Page.COMPANY)
  }


  render() {
    const {suggestions, keyword, input_data, validation_errors} = this.props.payment;

    return (
      <section className="panel company-info">
        <h3>{__("Enter Company Information")}</h3>
        <form className="form-horizontal" name="addCompanyInfo"
              id="PaymentsAddCompanyInfoForm" method="post" acceptCharset="utf-8">

          <FormTextBox
            id="PaymentsCompanyName"
            label={__("Company Name")}
            placeholder="株式会社ISAO"
            />
          <fieldset className="company-info-fieldset">
            <legend className="company-info-legend">{__("Company Address")}</legend>
            <FormTextBox
              id="PaymentsCompanyZipCode"
              label={__("Post Code")}
              placeholder="000-0000"
            />
            <FormTextBox
              id="PaymentsCompanyAddressRegion"
              label={__("Region")}
              placeholder="東京都"
            />
            <div className="form-group">
              <label htmlFor="PaymentsCompanyAddressCity"
                     className="circle-create-label">{__("City")}</label>
              <input
                name="data[Payments][company_address_city]" className="form-control" placeholder="台東区"
                type="text" id="PaymentsCompanyAddressCity"/>
            </div>
            <div className="form-group">
              <label htmlFor="PaymentsCompanyAddressStreet"
                     className="circle-create-label">{__("Address")}
              </label>
              <input
                name="data[Payments][company_address_street]" className="form-control" placeholder="台東１－１－１"
                type="text" id="PaymentsCompanyAddressStreet"/>
            </div>
          </fieldset>
          <fieldset className="company-info-fieldset">
            <legend className="company-info-legend">Company Contact</legend>
            <div className="form-group">
              <label htmlFor="PaymentsContactNameLast"
                     className="circle-create-label">{__("LastName")}
              </label>
              <input
                name="data[Payments][contact_name_last]" className="form-control" type="text"
                id="PaymentsContactNameLast"/>
            </div>
            <div className="form-group">
              <label htmlFor="PaymentsContactNameFirst"
                     className="circle-create-label">{__("FirstName")}
              </label>
              <input
                name="data[Payments][contact_name_first]" className="form-control" type="text"
                id="PaymentsContactNameFirst"/>
            </div>
          </fieldset>
          <div className="form-group">
            <label htmlFor="PaymentsContactEmail" className="circle-create-label">{__("Contact")}
              {__("Email")}
            </label>
            <input name="data[Payments][contact_email]" className="form-control"
                   placeholder="name@company.com" type="email"
                   id="PaymentsContactEmail"/>
          </div>
          <div className="form-group">
            <label htmlFor="PaymentsContactPhone"
                   className="circle-create-label">{__("Telephone")}
            </label>
            <input
              name="data[Payments][contact_phone]" className="form-control" placeholder="株式会社ISAO"

              type="tel" id="PaymentsContactPhone"/>
          </div>
          <div className="panel-footer setting_pannel-footer">
            <a className="btn btn-link design-cancel bd-radius_4px" href="/payments/">
              {__("Cancel")}</a>
            <input type="submit" className="btn btn-primary" defaultValue={__("Next")} />
          </div>
        </form>
      </section>
    )
  }
}
