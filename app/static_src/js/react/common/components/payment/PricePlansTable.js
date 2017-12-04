/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */

export default class PricePlansTable extends React.Component {
  constructor(props) {
    super(props);
    this.selectPricePlan = this.selectPricePlan.bind(this)
  }

  selectPricePlan(plan) {
    this.props.selectPricePlan(plan)
  }

  render() {
    const {price_plans, selected_price_plan_code, current_price_plan_code} = this.props
    const price_plans_el = () => {
      return price_plans.map((plan, i) => {
        let plan_el = ""
        if (current_price_plan_code == plan.code) {
          plan_el = <span className="">{__("Current plan")}</span>
        } else if (plan.can_select) {
          plan_el = <span
            onClick={() => {this.selectPricePlan(plan)}}
            className={`${selected_price_plan_code == plan.code ? 'fa fa-check success' : 'btn small'}`}>
                {plan.can_select && selected_price_plan_code != plan.code ? __('Select') : ''}
          </span>
        }
        const campaignId = plan.id
        return (
          <tr key={campaignId}>
            <td>{sprintf(__("%d members"), plan.max_members)}</td>
            <td>{plan.format_price}</td>
            <td>
              {plan_el}
            </td>
          </tr>
        )
      })
    }
    return (
      <table className="payment-table campaign-table">
        <thead>
        <tr>
          <td><strong>{__('Annual plan(paid monthly)')}</strong><br/>{__('max members')}</td>
          <td><strong>{__('Price')}</strong><br/>{__('per month')}</td>
          <td>&nbsp;</td>
        </tr>
        </thead>
        <tbody>
        {price_plans_el()}
        </tbody>
      </table>
    )
  }
}
PricePlansTable.propTypes = {
  selected_price_plan_code: React.PropTypes.string,
  current_price_plan_code: React.PropTypes.string,
  price_plans: React.PropTypes.array,
};
PricePlansTable.defaultProps = {
  selected_price_plan_code: "",
  current_price_plan_code: "",
  price_plans: []
};