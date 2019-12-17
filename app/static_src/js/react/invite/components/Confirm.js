/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory} from "react-router";
import {Link} from "react-router";
import {MaxLength} from "~/common/constants/App";
import Base from "~/common/components/Base";
import {Team} from "~/common/constants/Model";
import LoadingButton from "~/common/components/LoadingButton";

export default class Confirm extends Base {
  constructor(props) {
    super(props);
    this.state = {
      check_agreement: false
    }
    this.onChange = this.onChange.bind(this)
  }

  componentWillMount() {

    this.props.fetchConfirmInitialData()
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.invite.redirect_to_home) {
      super.removeBeforeUnloadHandler.apply(this)
      document.location.href = "/"
    }
    else if (nextProps.invite.redirect_to_upgrade_plan) {
        super.removeBeforeUnloadHandler.apply(this)
        browserHistory.push('/users/invite/exceed')
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this)
  }

  onSubmit(e) {
    e.preventDefault()
    // Save invitation
    this.props.saveInvitation()
  }

  onChange(e) {
    this.props.updateInputData({[e.target.name]: e.target.value}, "key_result")
  }

  onCheckAgreement(e) {
    this.setState({check_agreement: e.target.checked})
  }

  render() {
    const {confirm_data, emails, team, is_saving} = this.props.invite
    let emails_el = [];
    for (const i in emails) {
      emails_el.push(
        <li key={i}>{emails[i]}</li>
      );
    }
    const is_paid_plan = team.service_use_status == Team.SERVICE_USE_STATUS.PAID;
    const display_charge = is_paid_plan && confirm_data.charge_users_count > 0;
    return (
      <section className="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
        <form onSubmit={(e) => this.onSubmit(e)}>
          <div className="inviteCfmBlock">
            <h2 className="title">{__("Email Address")}({emails.length})</h2>
            <ul className="ml_5px">
              {emails_el}
            </ul>
          </div>
          <div className="inviteCfmBlock mod-bdt">
            <div className="ml_5px">
                {__("Goalous will automatically change the amount charged to your account whenever active members are added to your team.")}
            </div>
          </div>
          {display_charge &&
          <div className="inviteCfmBlock mod-bdt">
            <h2 className="title">{__("Billing")}</h2>
            <div className="ml_5px">
              <dl className="totalCharge">
                <dt className="totalCharge-label">{__("Total charge amount")}({__("Tax included")})</dt>
                <dd className="totalCharge-value">{confirm_data.total_charge}</dd>
              </dl>
              <div className="totalChargeFormula mb_12px">
                <div className="totalChargeFormula-block">
                  <span className="totalChargeFormula-num">
                    {confirm_data.charge_users_count}
                    <span className="totalChargeFormula-unit">{__("people")}</span>
                  </span>
                </div>
                <div className="totalChargeFormula-block">
                  <span className="totalChargeFormula-symbol">×</span>
                </div>
                <div className="totalChargeFormula-block">
                  <span className="totalChargeFormula-num">{confirm_data.amount_per_user}</span>
                </div>
                <div className="totalChargeFormula-block">
                  <span className="totalChargeFormula-symbol">×</span>
                </div>
                <div className="totalChargeFormula-block">
                  <div className="totalChargeFormula-fraction">
                    <span className="totalChargeFormula-num">{confirm_data.use_days_by_next_base_date}<span
                      className="totalChargeFormula-unit">{__("days")}</span></span>
                    <span className="totalChargeFormula-fraction-exp"/>
                    <span className="totalChargeFormula-num">1<span
                      className="totalChargeFormula-unit">{__("month")}</span></span>
                  </div>
                </div>
              </div>
              <table className="totalChargeFormulaDetail mb_16px">
                <tbody>
                <tr>
                  <th className="totalChargeFormulaDetail-item">{confirm_data.charge_users_count} {__("people")}</th>
                  <td className="totalChargeFormulaDetail-description">：{__("Number of users")}</td>
                </tr>
                <tr>
                  <th className="totalChargeFormulaDetail-item">{confirm_data.amount_per_user}</th>
                  <td className="totalChargeFormulaDetail-description">：{`${__("Price per user")}(${__("Tax excluded")})`}</td>
                </tr>
                <tr>
                  <th className="totalChargeFormulaDetail-item">{confirm_data.use_days_by_next_base_date} {__("days")}</th>
                  <td className="totalChargeFormulaDetail-description">：{__("Number of days")}({__("Daily payment")})</td>
                </tr>
                </tbody>
              </table>
              <a href="/pricing?backBtn=true" target="_blank">
                <i className="fa fa-question-circle mr_4px" aria-hidden="true"/>{__("View details")}
              </a>
            </div>
          </div>
          }
          {display_charge &&
          <div className="serviceTermAgreement mb_8px">
            <label>
              <input
                type="checkbox" name="service_term_agreement" className="serviceTermAgreement-cb"
                onChange={(e) => this.onCheckAgreement(e)}
              />{__("I confirmed the billing content")}
            </label>
          </div>
          }
          <div className="btnGroupForForm">
            <button type="submit" className="btnGroupForForm-next" ref="submit"
                    disabled={(display_charge && !this.state.check_agreement) || is_saving ? "disabled" : ""}>
              {display_charge ? __("Purchase & Invite") : __("Send")}
            </button>
            <Link className="btnGroupForForm-cancel" to="/users/invite">{__("Back")}</Link>
          </div>
        </form>
      </section>
    )
  }
}
