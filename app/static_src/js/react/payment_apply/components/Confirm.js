/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "~/payment_apply/constants/Page";
import Base from "~/common/components/Base";
import ConfirmCharge from "~/common/components/elements/payment/ConfirmCharge";
import {PaymentSetting} from "~/common/constants/Model";
import LoadingButton from "~/common/components/LoadingButton";

export default class Confirm extends Base {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.CONFIRM)
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.payment.to_next_page) {
      browserHistory.push(Page.URL_COMPLETE)
    }
  }

  componentWillUnmount() {
    this.props.resetStates();
    super.componentWillUnmount.apply(this)
  }

  onSubmit(e) {
    e.preventDefault();
    this.props.savePaymentInvoice()
  }

  render() {
    const {payment} = this.props;
    let tax = payment.tax;
    let sub_total_charge = payment.sub_total_charge;
    let total_charge = payment.total_charge;
    let campaign_members = 0;
    if (payment.is_campaign_team) {
      const input_campaign = payment.selected_price_plan;
      tax = input_campaign.tax;
      sub_total_charge = input_campaign.sub_total_charge;
      total_charge = input_campaign.total_charge;
      campaign_members = input_campaign.members;
    }
    return (
      <section className="panel payment enter-cc-info">
        <form className="form-horizontal"
              onSubmit={(e) => this.onSubmit(e)}>
          <div className="panel-container">
            <h3>{__('Confirm registration and charge')}</h3>
            <ConfirmCharge
              amount_per_user={payment.amount_per_user}
              charge_users_count={payment.charge_users_count}
              sub_total_charge={sub_total_charge}
              tax={tax}
              total_charge={total_charge}
              is_campaign={payment.is_campaign_team}
              campaign_members={campaign_members}
            />
          </div>
          <div className="panel-footer setting_pannel-footer">
            <Link className="btn btn-link design-cancel bd-radius_4px" to="/payments/apply/invoice">
              {__("Back")}
            </Link>
            {(() => {
              if (payment.is_saving) {
                return <LoadingButton/>
              } else {
                return (
                  <button className="btn btn-primary">
                    {payment.is_campaign_team ? __("Agree & Purchase") : __("Purchase")}
                  </button>
                )
              }
            })()}
          </div>
        </form>
      </section>
    )
  }
}
