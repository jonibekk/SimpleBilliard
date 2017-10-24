/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import {PaymentSetting} from "~/common/constants/Model";

export default class Campaign extends Base {
  constructor(props) {
    super(props);
    this.onChange = this.onChange.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.CAMPAIGN)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.payment.to_next_page) {
      browserHistory.push(Page.URL_COMPANY)
    }
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  onChange(price_plan_id) {
    this.props.updateInputData({ price_plan_id }, 'price_plan_purchase_team')
    this.props.validatePayment(Page.CAMPAIGN, {payment_setting: { price_plan_id }});
  }

  // TODO: Merge @joshua 's view file
  render() {
    const { payment } = this.props
    const campaigns_el = () => {
      return payment.campaigns.map((campaign, i) => {
        return (
          <tr>
            <td>{ campaign.member_count }{ __('members')}</td>
            <td>{ campaign.total_charge }</td>
            <td><a onClick={ () => { this.onChange(campaign.id) } } key={ campaign.id} className="btn small">{__('Select')}</a></td>
          </tr>
        )
      })
    }
    return (
      <section className="panel payment">
        <div className="panel-container">
            <h3>{ __("Select Plan") }</h3>
            <p>{ __('You have 187 active members. Please select the best plan for the number of members expected for your team.') }</p>
            <table className="payment-table campaign-table">
                <thead>
                    <tr>
                        <td><strong>{ __('Plan')}</strong><br />{ __('max members')}</td>
                        <td><strong>{ __('Price')}</strong><br />{ __('per month')}</td>
                        <td>&nbsp;</td>
                    </tr>
                </thead>
                <tbody>
                    { campaigns_el() }
                </tbody>
            </table>
            <p>{ __('Larger plans available on request. All prices are without tax.')}</p>
        </div>
        <div className="panel-footer setting_pannel-footer">
            <a className="btn btn-link design-cancel bd-radius_4px" href="/payments/method">
                { __("Cancel") }
            </a>
            <input type="submit" className="btn btn-primary" value={ __('Update') }disabled="disabled" />
        </div>
      </section>
    )
  }
}
