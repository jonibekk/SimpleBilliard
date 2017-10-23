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

  render() {
    return (
      <div>
        <Link className="btn btn-link design-cancel bd-radius_4px" to="/payments/apply">
          {__("Back")}
        </Link>
      </div>
    )
  }
}
