/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import ReactDOM from "react-dom";
import {browserHistory, Link} from "react-router";
import * as Page from "~/payment_apply/constants/Page";
import Base from "~/common/components/Base";

export default class CreditCard extends Base {
  constructor(props) {
    super(props);
    this.state = {};
  }

  componentWillMount() {
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.payment.to_next_page) {
      browserHistory.push(Page.URL_COMPLETE)
    }
  }

  componentWillUnmount() {
    this.props.resetStates();
    super.componentWillUnmount.apply(this)
  }

  render() {
    return (
      <section className="panel">
      </section>
    )
  }
}
