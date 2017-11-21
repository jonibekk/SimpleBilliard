/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import PricePlansTable from "~/common/components/payment/PricePlansTable";
import {PaymentSetting} from "~/common/constants/Model";

export default class Campaign extends Base {
  constructor(props) {
    super(props);
    this.onClickNext = this.onClickNext.bind(this)
    this.selectPricePlan = this.selectPricePlan.bind(this)
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

  componentWillUnmount() {
    this.props.resetStates()
    super.componentWillUnmount.apply(this)
  }

  onClickNext() {
    const {price_plan_code} = this.props.payment.input_data.price_plan_purchase_team
    this.props.validatePayment(Page.CAMPAIGN, {price_plan_purchase_team: {price_plan_code}});
  }

  selectPricePlan(plan) {
    this.props.updateSelectedCampaignPlanInfo({
      id: plan.id,
      tax: plan.tax,
      sub_total_charge: plan.sub_total_charge,
      total_charge: plan.total_charge,
      members: plan.member_count
    })
    this.props.updateInputData(
      {price_plan_code: plan.code},
      'price_plan_purchase_team'
    )
  }

  render() {
    const {payment} = this.props
    const selected_price_plan_code = payment.input_data.price_plan_purchase_team.price_plan_code
    return (
      <section className="panel payment">
        <div className="panel-container">
          <h3>{__('Select Plan')}</h3>
          <p>{sprintf(__('You have %d active members. Please select the best plan for the number of members expected for your team.'), payment.charge_users_count)}</p>
          <PricePlansTable
            price_plans={payment.price_plans}
            selected_price_plan_code={selected_price_plan_code}
            selectPricePlan={(plan) => this.selectPricePlan(plan)}
          />
          <p>{__('Larger plans available on request. All prices are without tax.')}</p>
        </div>
        <div className="panel-footer setting_pannel-footer">
          <Link className="btn btn-link design-cancel bd-radius_4px"
                to="/payments/apply">
            {__("Back")}
          </Link>
          <a className="btn btn-primary"
             onClick={this.onClickNext}
             disabled={!selected_price_plan_code}>
            {__('Next')}
          </a>
        </div>
      </section>
    )
  }
}
