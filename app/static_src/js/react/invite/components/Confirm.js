/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {Link} from "react-router";
import {MaxLength} from "~/common/constants/App";
import Base from "~/common/components/Base";
import {Team} from "~/common/constants/Model";

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
    this.setState({check_agreement: e.target.value})
  }

  render() {
    const {confirm_data, emails, team} = this.props.invite
    let emails_el = [];
    for (const i in emails) {
      emails_el.push(
        <li key={i}>{emails[i]}</li>
      );
    }
    const is_paid_plan = team.service_use_status == Team.SERVICE_USE_STATUS.PAID;
    return (
      <section className="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
        <form onSubmit={(e) => this.onSubmit(e)}>
          <div className="inviteCfmBlock">
            <h2 className="title">招待先メールアドレス({emails.length})</h2>
            <ul className="ml_5px">
              {emails_el}
            </ul>
          </div>
          {is_paid_plan &&
          <div className="inviteCfmBlock mod-bdt">
            <h2 className="title">請求料金</h2>
            <div className="ml_5px">
              <dl className="totalCharge">
                <dt className="totalCharge-label">請求料金合計</dt>
                <dd className="totalCharge-value">{confirm_data.total_charge}</dd>
              </dl>
              <div className="totalChargeFormula mb_12px">
                <div className="totalChargeFormula-block">
                  <span className="totalChargeFormula-num">
                    {confirm_data.charge_users_count}
                    <span className="totalChargeFormula-unit">people</span>
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
                      className="totalChargeFormula-unit">days</span></span>
                    <span className="totalChargeFormula-fraction-exp"/>
                    <span className="totalChargeFormula-num">1<span
                      className="totalChargeFormula-unit">month</span></span>
                  </div>
                </div>
              </div>
              <table className="totalChargeFormulaDetail mb_16px">
                <tbody>
                <tr>
                  <th className="totalChargeFormulaDetail-item">{confirm_data.charge_users_count} people</th>
                  <td className="totalChargeFormulaDetail-description">：請求人数</td>
                </tr>
                <tr>
                  <th className="totalChargeFormulaDetail-item">{confirm_data.amount_per_user}</th>
                  <td className="totalChargeFormulaDetail-description">：1ヶ月の利用料金</td>
                </tr>
                <tr>
                  <th className="totalChargeFormulaDetail-item">{confirm_data.use_days_by_next_base_date} days</th>
                  <td className="totalChargeFormulaDetail-description">：利用日数(日割り)</td>
                </tr>
                </tbody>
              </table>
              <a href>
                <i className="fa fa-question-circle mr_4px" aria-hidden="true"/>詳細はこちら
              </a>
            </div>
          </div>
          }
          {is_paid_plan &&
          <div className="serviceTermAgreement mb_8px">
            <label>
              <input
                type="checkbox" name="service_term_agreement" className="serviceTermAgreement-cb"
                onChange={(e) => this.onCheckAgreement(e)}
              />請求内容を確認しました
            </label>
          </div>
          }
          <div className="btnGroupForForm">
            <button type="submit" className="btnGroupForForm-next" ref="submit"
                    disabled={(is_paid_plan && !this.state.check_agreement) ? "disabled" : ""}>
              送信する
            </button>
            <Link className="btnGroupForForm-cancel" to="/users/invite">戻る</Link>
          </div>
        </form>
      </section>
    )
  }
}
