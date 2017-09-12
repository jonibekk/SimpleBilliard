import React from "react";
import {connect} from "react-redux";

class ConfirmCharge extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div>
        <div className="payment-info-group">
          <strong>{__('Price per user')}:&nbsp;</strong><span
          className="cc-info-value">{this.props.amount_per_user}</span><br/>
          <strong>{__('Number of users')}:&nbsp;</strong><span
          className="cc-info-value">{this.props.charge_users_count}</span><br/>
          <strong>{__('Sub Total')}:&nbsp;</strong><span className="cc-info-value">{this.props.sub_total_charge}</span><br/>
          <strong>{__('Tax')}:&nbsp;</strong><span className="cc-info-value">{this.props.tax}</span><br/>
          <hr/>
          <strong>{__('Total')}:&nbsp;</strong><span className="cc-info-value">{this.props.total_charge}</span>
        </div>
        <div className="form-group">
          <a href="/terms" target="_blank">{__("Terms of Use")}</a>
        </div>
      </div>
    )

  }
}

ConfirmCharge.propTypes = {
  amount_per_user: React.PropTypes.string,
  charge_users_count: React.PropTypes.number,
  sub_total_charge: React.PropTypes.string,
  tax: React.PropTypes.string,
  total_charge: React.PropTypes.string,
};
ConfirmCharge.defaultProps = {
  amount_per_user: "",
  charge_users_count: 0,
  sub_total_charge: "",
  tax: "",
  total_charge: "",

};
export default connect()(ConfirmCharge);
