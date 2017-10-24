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
          <a onClick={ () => { this.onChange(campaign.id) } } key={ campaign.id}>
            { campaign.total_charge }
          </a>
        )
      })
    }
    return (
      <section className="panel payment">
        <div>
          <Link className="btn btn-link design-cancel bd-radius_4px" to="/payments/apply">
            {__("Back")}
          </Link>
          { __("members") }
          <ul>{ campaigns_el() }</ul>
        </div>
      </section>
    )
  }
}
