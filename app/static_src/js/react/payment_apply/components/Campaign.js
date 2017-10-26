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
    this.state = {
      selected_campaign: { id: null }
    }
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
    const campaign = this.state.selected_campaign
    if (campaign.id != null) {
      this.props.updateInputData({
        id: campaign.id,
        tax: campaign.tax,
        sub_total_charge: campaign.sub_total_charge,
        total_charge: campaign.total_charge
      }, 'price_plan_purchase_team')
      // This is duplication, I know..
      // But for credit card post data, it need contain price_plan_id on payment_setting
      this.props.updateInputData({
        price_plan_id: campaign.id,
      }, 'payment_setting')
      this.props.validatePayment(Page.CAMPAIGN, { payment_setting: { id: campaign.id } });
    }
  }

  selectCampaign(campaign) {
    this.setState({
      selected_campaign: campaign
    });
  }

  render() {
    const { payment } = this.props
    const campaigns_el = () => {
      return payment.campaigns.map((campaign, i) => {
        const campaignId = campaign.id
        const display_select_button = payment.charge_users_count < campaign.member_count
        return (
          <tr key={ campaignId }>
            <td>{ campaign.member_count }{ __('members')}</td>
            <td>{ campaign.sub_total_charge }</td>
            <td>
              { display_select_button &&
                <a onClick={ () => { this.selectCampaign(campaign) } }
                   className={ `btn small ${this.state.selected_campaign.id == campaignId ? 'selected' : ''}` }>
                  { __('Select') }
                </a>
              }
            </td>
          </tr>
        )
      })
    }
    return (
      <section className="panel payment">
        <div className="panel-container">
          <h3>{ __('Select Plan') }</h3>
          <p>{ __(`You have ${payment.charge_users_count} active members. Please select the best plan for the number of members expected for your team.`) }</p>
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
          <Link className="btn btn-link design-cancel bd-radius_4px"
                to="/payments/apply">
            { __("Back") }
          </Link>
          <a className="btn btn-primary"
             onClick={ this.onClickNext }
             disabled={ this.state.selected_campaign.id == null } >
            { __('Next') }
          </a>
        </div>
      </section>
    )
  }
}
