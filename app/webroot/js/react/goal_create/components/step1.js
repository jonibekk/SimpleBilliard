import React from "react";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import InvalidMessageBox from "./elements/InvalidMessageBox";

export default class Step1Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      toNextPage: false,
    };
    this.handleChange = this.handleChange.bind(this)
  }

  componentWillMount() {
    // this.props.fetchInitialData(Page.STEP1)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      browserHistory.push(Page.URL_STEP2)
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.validateGoal(Page.STEP1)
  }
  handleChange(e) {
    this.props.updateInputData({name: e.target.value})
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set your goal name")}</h1>
        <p
          className="goals-create-description">{__("Your name will displayed along with your goals and posts in Goalous.")}</p>
        <div className="goals-create-dispaly-vision">
          <h2
            className="goals-create-dispaly-vision-title">{__("Vision : Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut laboreet dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquipex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eufugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deseruntmollit anim id est laborum")}
          </h2>
          <div className="goals-create-dispaly-vision-detail">
            <img className="goals-create-dispaly-vision-detail-image" alt width={32} height={32}/>
            <div className="goals-create-dispaly-vision-detail-info">
              <p className="goals-create-dispaly-vision-text">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                deserunt mollit anim id est laborum.
              </p>
              <a className="goals-create-dispaly-vision-text-more">{__("more")}</a>
            </div>
          </div>
          <a className="goals-create-dispaly-vision-see-other"><i className="fa fa-refresh" aria-hidden="true"/> <span
            className="goals-create-interactive-link">{__("See Other 7")}</span></a>
        </div>
        <form className="goals-create-input" action onSubmit={(e) => this.handleSubmit(e) }>
          <label className="goals-create-input-label">{__("Goal name?")}</label>
          <input name="name" className="form-control goals-create-input-form" type="text"
                 placeholder="e.g. Get goalous users" ref="name"
                 onChange={this.handleChange}/>
          <InvalidMessageBox message={this.props.goal.validationErrors.name}/>
          <a className="goals-create-show-sample"><i className="fa fa-eye" aria-hidden="true"/> <span
            className="goals-create-interactive-link">{__("Show sample")}</span></a>
          <div className="row">
            <button type="submit" className="goals-create-btn-next btn">{__("Next →")}</button>
            <a className="goals-create-btn-cancel btn" href="/">{__("Cancel")}</a>
          </div>
        </form>
      </section>
    )
  }
}
