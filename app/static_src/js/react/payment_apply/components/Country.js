// TODO.Payment: Translate untranslated sentence
/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import {PaymentSetting} from "~/common/constants/Model";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";

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
    const {countries, lang_code, input_data, validation_errors} = this.props.payment
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
    countries_option_el.push(<option key="" value="">{__('Please select')}</option>)
    for (const code in countries) {
      countries_option_el.push(
        <option key={code} value={code}>{countries[code]}</option>
      );
    }

    const errors_payment_setting = validation_errors.payment_setting ? validation_errors.payment_setting : {};

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
            <InvalidMessageBox message={errors_payment_setting.company_country}/>

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
          <div className="mt_32px">
            <div className="payment-option-title-wrapper">
              <h3>{__('Select Payment Method')}</h3>
            </div>
            <div className="payment-option-container">
              <div className="payment-option"
                   onClick={(e) => this.choosePaymentType(PaymentSetting.PAYMENT_TYPE.CREDIT_CARD)}>
                <h4>{__('Credit Card')}</h4>
                <i className="fa fa-credit-card"/>
                <p>{__("You can use Visa, MasterCard, AmericanExpress, Discover, Diners Club and JCB.")}</p>
                <a href="#">{__('Setup')}</a>
              </div>
              <div className="payment-option"
                   onClick={(e) => this.choosePaymentType(PaymentSetting.PAYMENT_TYPE.INVOICE)}>
                <h4>{__('Invoice')}</h4>
                <i className="fa fa-leaf"/>
                <p>{__("Invoice will be issued monthly, so please transfer by the deadline.")}</p>
                <a href="#" className="payment-option-setup-link">{__('Setup')}</a>
              </div>
            </div>
          </div>
          }
        </div>
      </section>
    )
  }
}
