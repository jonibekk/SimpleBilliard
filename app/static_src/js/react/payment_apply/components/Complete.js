/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import Base from "~/common/components/Base";
import {nl2br} from "~/util/element";

export default class Complete extends Base {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <section className="panel payment-thanks">
        <div className="panel-container">
          <h3 className="thank-you-headline">{__('Thank You')}</h3>
          <p className="thank-you-text">
            {__("Your transaction and registration to the payment plan was successful.")}
            <br/>
            {__("In the case of invoice payment, we conduct a credit check. As a result of the investigation, we will contact you if we deem it impossible to trade.")}
          </p>
          <a className="" href="/payments">{__('Move to Billing page')}</a>
        </div>
        <div className="confetti-cannon">
        </div>
      </section>
    )
  }
}
