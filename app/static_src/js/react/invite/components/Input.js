/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory} from "react-router";
import {MaxLength} from "~/common/constants/App";
import Base from "~/common/components/Base";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";

export default class Input extends Base {
  constructor(props) {
    super(props);
    this.onChange = this.onChange.bind(this)
  }

  componentWillMount() {
    this.props.fetchInputInitialData()
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.invite.to_next_page) {
      browserHistory.push('/users/invite/confirm')
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this)
  }

  onSubmit(e) {
    e.preventDefault()
    this.props.validateInvitation()
  }

  onChange(e) {
    this.props.updateInputData({emails: e.target.value})
  }

  render() {
    const {input_data, validation_errors} = this.props.invite
    let err_msg_el = [];
    for (const i in validation_errors) {
      err_msg_el.push(
        <InvalidMessageBox key={i} message={validation_errors[i]}/>
      );
    }
    return (
      <section className="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
        <h2 className="gl-form-heading">{__("Invite members")}</h2>
        <form onSubmit={(e) => this.onSubmit(e)}>
          <div className="mb_16px">
            <label className="gl-form-label">{__("Email Address")}</label>
            <p className="gl-form-guide">{__("You can set email addresses by comma(,) separated or by newline separated.")}</p>
            <textarea
              name="email"
              className="form-control"
              onChange={this.onChange}
              value={input_data.emails}
              rows="6"
              placeholder="test1@example.com&#13;&#10;test1@example.com"
            />
          </div>
          {err_msg_el}
          <div className="btnGroupForForm">
            <button type="submit" className="btnGroupForForm-next" disabled={input_data.emails ? "" : "disabled"}>{__("Next")} →
            </button>
            <a className="btnGroupForForm-cancel" href="/">{__("Cancel")}</a>
          </div>
        </form>
      </section>
    )
  }
}
