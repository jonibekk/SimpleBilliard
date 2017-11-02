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
    this.onClickNext = this.onClickNext.bind(this)
    this.selectCampaign = this.selectCampaign.bind(this)
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
    const {price_plan_id} = this.props.payment.input_data.price_plan_purchase_team
    this.props.validatePayment(Page.CAMPAIGN, {price_plan_purchase_team: { price_plan_id}});
  }

  selectCampaign(campaign) {
    this.props.updateSelectedCampaignPlanInfo({
      id: campaign.id,
      tax: campaign.tax,
      sub_total_charge: campaign.sub_total_charge,
      total_charge: campaign.total_charge,
      members: campaign.member_count
    })
    this.props.updateInputData(
      {price_plan_id: campaign.id},
      'price_plan_purchase_team'
    )
  }

  render() {
    const {payment} = this.props
    const selected_campaign_plan_id = payment.input_data.price_plan_purchase_team.price_plan_id
    const campaigns_el = () => {
      return payment.campaigns.map((campaign, i) => {
        const campaignId = campaign.id
        const display_select_button = payment.charge_users_count <= campaign.member_count
        return (
          <tr key={campaignId}>
            <td>{sprintf(__("%d members"), campaign.member_count)}</td>
            <td>{campaign.sub_total_charge}</td>
            <td>
              {display_select_button &&
              <span onClick={() => {
                this.selectCampaign(campaign)
              }}
                    className={`${selected_campaign_plan_id == campaignId ? 'fa fa-check success' : 'btn small'}`}>
                  {selected_campaign_plan_id == campaignId ? '' : __('Select')}
                </span>
              }
            </td>
          </tr>
        )
      })
    }
    return (
      <section className="panel payment">
        <div className="panel-container">
          <h3>{__('Select Plan')}</h3>
          <p>{sprintf(__('You have %d active members. Please select the best plan for the number of members expected for your team.'), payment.charge_users_count)}</p>
          <table className="payment-table campaign-table">
            <thead>
            <tr>
              <td><strong>{__('Plan')}</strong><br/>{__('max members')}</td>
              <td><strong>{__('Price')}</strong><br/>{__('per month')}</td>
              <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            {campaigns_el()}
            </tbody>
          </table>
          <p>{__('Larger plans available on request. All prices are without tax.')}</p>
        </div>
        <div className="panel-footer setting_pannel-footer">
          <Link className="btn btn-link design-cancel bd-radius_4px"
                to="/payments/apply">
            {__("Back")}
          </Link>
          <a className="btn btn-primary"
             onClick={this.onClickNext}
             disabled={selected_campaign_plan_id == null}>
            {__('Next')}
          </a>
        </div>
      </section>
    )
  }
}
