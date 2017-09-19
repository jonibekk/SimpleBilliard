/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "~/payment_apply/constants/Page";
import Base from "~/common/components/Base";
import ConfirmCharge from "~/payment_apply/components/elements/ConfirmCharge";
import {PaymentSetting} from "~/common/constants/Model";

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
    const {payment} = this.props
    return (
      <section className="panel enter-cc-info">
        <h3>{__('Confirm registration and charge')}</h3>
        <form className="form-horizontal"
              onSubmit={(e) => this.onSubmit(e)}>
          <ConfirmCharge
            amount_per_user={payment.amount_per_user}
            charge_users_count={payment.charge_users_count}
            sub_total_charge={payment.sub_total_charge}
            tax={payment.tax}
            total_charge={payment.total_charge}
          />
          <div className="panel-footer setting_pannel-footer">
            <Link className="btn btn-link design-cancel bd-radius_4px" to="/payments/apply/invoice">
              {__("Back")}
            </Link>
            <button className="btn btn-primary">
              {__("Register")}
            </button>
          </div>
        </form>
      </section>
    )
  }
}
