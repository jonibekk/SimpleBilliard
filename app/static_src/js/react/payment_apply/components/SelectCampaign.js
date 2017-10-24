/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";
import {PaymentSetting} from "~/common/constants/Model";

export default class SelectCampaign extends Base {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.CAMPAIGN)
  }

  choose(campaign_id) {

  }

  render() {
    const { payment } = this.props
    const campaigns_el = () => {
      return payment.campaigns.map((campaign, i) => {
        return (
          <a onClick={ this.choose(campaign.id) }>
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
