/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import Base from "~/common/components/Base";

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
            {
              __('Your transaction was successful. You and your team now have full access to Goalous.')
            }
          </p>
          <p className="thank-you-text">{__('Go achieve your goal!')}</p>
          <a className="" href="/payments">{__('Move to Billing page')}</a>
        </div>
        <div className="confetti-cannon">
        </div>
      </section>
    )
  }
}
