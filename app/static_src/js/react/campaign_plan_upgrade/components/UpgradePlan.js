/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import Base from "~/common/components/Base";
import PricePlansTable from "~/common/components/payment/PricePlansTable";
import ConfirmCharge from "~/common/components/payment/ConfirmCharge";
import LoadingButton from "~/common/components/LoadingButton";

export default class UpgradePlan extends Base {
  constructor(props) {
    super(props);
    this.selectPricePlan = this.selectPricePlan.bind(this)
    this.onSubmit = this.onSubmit.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData()
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillUnmount() {
    this.props.resetStates()
    super.componentWillUnmount.apply(this)
  }

  onSubmit() {
    const {code} = this.props.campaign.selected_price_plan
    this.props.upgradePricePlan(code);
  }

  selectPricePlan(plan) {
    this.props.selectPricePlan(plan)
  }

  render() {
    const {campaign} = this.props;
    const selected_price_plan = campaign.selected_price_plan;
    return (
      <section className="panel payment">
        <div className="panel-container">
          <h3>{__('Select Plan')}</h3>
          <p>{sprintf(__('You have %d active members. Please select the best plan for the number of members expected for your team.'), campaign.charge_users_count)}</p>
          <PricePlansTable
            price_plans={campaign.price_plans}
            selected_price_plan_code={selected_price_plan.code}
            selectPricePlan={(plan) => this.selectPricePlan(plan)}
          />
          <p>{__('Larger plans available on request. All prices are without tax.')}</p>
          {selected_price_plan.code &&
            <ConfirmCharge
              charge_users_count={campaign.charge_users_count}
              sub_total_charge={selected_price_plan.sub_total_charge}
              tax={selected_price_plan.tax}
              total_charge={selected_price_plan.total_charge}
              is_campaign={true}
              campaign_members={selected_price_plan.max_members}
            />
          }
        </div>
        <div className="panel-footer setting_pannel-footer">
          <a className="btn btn-link design-cancel bd-radius_4px"
                href="/payments">
            {__("Cancel")}
          </a>
          {campaign.is_saving ? <LoadingButton/> :
            <a className="btn btn-primary"
               onClick={this.onSubmit}
            >
              {__('Agree & Purchase')}
            </a>
          }
        </div>
      </section>
    )
  }
}
