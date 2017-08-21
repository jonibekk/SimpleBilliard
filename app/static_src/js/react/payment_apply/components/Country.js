// TODO.Payment: Translate untranslated sentence
/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import {PaymentSetting} from "~/common/constants/Model";

export default class Country extends Base {
  constructor(props) {
    super(props);
    this.state = {};
    this.onChange = this.onChange.bind(this)
    this.choosePaymentType = this.choosePaymentType.bind(this)

  }

  componentWillMount() {
    this.props.fetchInitialData(Page.COUNTRY)
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.payment.to_next_page) {
      browserHistory.push(Page.URL_COMPANY)
    }
  }

  componentWillUnmount() {
    this.props.resetStates();
    super.componentWillUnmount.apply(this)
  }

  onSubmit(e) {
    e.preventDefault()
    this.props.validatePayment(Page.COUNTRY, {
      payment_setting: {
        type: PaymentSetting.PAYMENT_TYPE.CREDIT_CARD
      }
    });
  }

  choosePaymentType(type) {
    this.props.validatePayment(Page.COUNTRY, {payment_setting: {type}});
  }


  onChange(e) {
    this.props.updateInputData({company_country: e.target.value}, 'payment_setting')
  }

  render() {
    const {countries, lang_code, input_data} = this.props.payment
    if (Object.keys(countries).length == 0) {
      return (
        <section className="panel choose-payment">
          <div className="panel-container">
          </div>
        </section>
      )
    }

    let is_ja = false;
    const {company_country} = input_data.payment_setting
    if (company_country == "JP") {
      is_ja = true;
    } else if (company_country == "" && lang_code == 'ja') {
      is_ja = true;
    }

    let countries_option_el = [];
    countries_option_el.push(<option key="" value="">{__('Choose Country')}</option>)
    for (const code in countries) {
      countries_option_el.push(
        <option key={code} value={code}>{countries[code]}</option>
      );
    }

    return (
      <section className="panel choose-payment">
        <div className="panel-container">
          <form className="form-horizontal" name="companyLocation" onSubmit={(e) => this.onSubmit(e)}>
            <h3>{__('Select Country Location')}</h3>
            <select
              name="company_country"
              className="form-control setting_input-design company-location-select"
              value={company_country}
              onChange={(e) => this.onChange(e)}
              ref="company_country"
            >
              {countries_option_el}
            </select>
            {!is_ja &&
            <div className="clearfix">
              <button
                className="btn btn-primary"
                disabled={input_data.country == "" ? "disabled" : ""}>
                {__("Next")}
              </button>
            </div>
            }
          </form>
          {is_ja &&
          <div className="payment-option-container mt_32px">
            <h3>{__('Select Payment Method')}</h3>
            <div className="payment-option"
                 onClick={(e) => this.choosePaymentType(PaymentSetting.PAYMENT_TYPE.CREDIT_CARD)}>
              <h4>{__('Credit Card')}</h4>
              <i className="fa fa-credit-card"/>
              <p>{__("Use a credit card to setup automatic, reoccuring payments for your Goalous team.")}</p>
              <a href="#">{__('Setup')}</a>
            </div>
            <div className="payment-option"
                 onClick={(e) => this.choosePaymentType(PaymentSetting.PAYMENT_TYPE.INVOICE)}>
              <h4>{__('Invoice')}</h4>
              <i className="fa fa-leaf"/>
              <p>{__("Setup a monthly invoice with Goalous.")}</p>
              <a href="#" className="payment-option-setup-link">{__('Setup')}</a>
            </div>
          </div>
          }

        </div>
      </section>
    )
  }
}
