/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "~/payment_apply/constants/Page";
import Base from "~/common/components/Base";
import ConfirmCharge from "~/payment_apply/components/elements/ConfirmCharge";

export default class Confirm extends Base {
  constructor(props) {
    super(props);
    this.onChange = this.onChange.bind(this)
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
        <h3>{__('Enter Payment Information')}</h3>
        <form id="enterCCInfo" className="form-horizontal" name=""
              onSubmit={(e) => this.onSubmit(e)}>
          <ConfirmCharge />
          <div className="panel-footer setting_pannel-footer">
            <Link className="btn btn-link design-cancel bd-radius_4px" to="/payments/apply/company">
              {__("Back")}
            </Link>
            <button
              className="btn btn-primary"
              disabled={payment.is_disabled_submit ? "disabled" : ""}>
              {__("Register")}
            </button>
          </div>
        </form>
      </section>
    )
  }
}
