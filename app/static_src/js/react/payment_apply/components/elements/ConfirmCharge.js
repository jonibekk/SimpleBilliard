import React from "react";
import ReactDOMServer from 'react-dom/server';
import {connect} from "react-redux";

class ConfirmCharge extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const tms = (<a href="/terms?backBtn=true"  target="_blank">{__("Terms of Use")}</a>);
    const contract = (<a href="/campaign_terms?backBtn=true" target="_blank">{__("Campaign Contract")}</a>);

    return (
        <div className="payment-info-group">
          {!this.props.is_campaign &&
            <div>
              <strong>{__('Price per user')}:&nbsp;</strong><span
              className="info-value">{this.props.amount_per_user}</span><br/>
            </div>
          }
          <strong>{__('Number of users')}:&nbsp;</strong><span className="info-value">{this.props.charge_users_count}</span><br/>
          <strong>{__('Sub Total')}:&nbsp;</strong><span className="info-value">{this.props.sub_total_charge}</span><br/>
          <strong>{__('Tax')}:&nbsp;</strong><span className="info-value">{this.props.tax}</span><br/>
          <div className="hr"></div>
          <strong>{__('Total')}:&nbsp;</strong><span className="info-value">{this.props.total_charge}</span>
          {!this.props.is_campaign ? (
            tms
          ) : (
            <p><span dangerouslySetInnerHTML={{__html:sprintf(__("By purchasing, you agree to the %s and %s."),
                ReactDOMServer.renderToString(contract),
                ReactDOMServer.renderToString(tms))}}></span></p>
          )
          }

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
  is_campaign: React.PropTypes.boolean
};
ConfirmCharge.defaultProps = {
  amount_per_user: "",
  charge_users_count: 0,
  sub_total_charge: "",
  tax: "",
  total_charge: "",
  is_campaign: false
};
export default connect()(ConfirmCharge);
