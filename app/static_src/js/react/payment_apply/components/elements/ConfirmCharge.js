import React from "react";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import {connect} from "react-redux";
import {updateInputData} from "~/payment_apply/actions/index";

class ConfirmCharge extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div className="payment-info-group">
        <strong>{__('Price per user')}:&nbsp;</strong><span
        className="cc-info-value">{payment.amount_per_user}</span><br/>
        <strong>{__('Number of users')}:&nbsp;</strong><span
        className="cc-info-value">{payment.charge_users_count}</span><br/>
        <strong>{__('Sub Total')}:&nbsp;</strong><span className="cc-info-value">$1999.00</span><br/>
        <strong>{__('Tax')}:&nbsp;</strong><span className="cc-info-value">$159.92</span><br/>
        <hr/>
        <strong>{__('Total')}:&nbsp;</strong><span className="cc-info-value">{payment.total_charge}</span>
      </div>
    )

  }
}

ConfirmCharge.propTypes = {
  amount_per_user: React.PropTypes.string,
  charge_users_count: React.PropTypes.number,
  max_length: React.PropTypes.number,
};
ConfirmCharge.defaultProps = {
  amount_per_user: "",
  charge_users_count: 0,
  max_length: 255
};
export default connect()(ConfirmCharge);
