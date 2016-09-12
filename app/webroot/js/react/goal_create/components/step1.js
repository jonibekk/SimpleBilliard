import React from "react";
import {browserHistory} from "react-router";
import ReactDOM from "react-dom";
import * as Page from "../constants/Page";
import {InvalidMessageBox} from "./elements/invalid_message_box";

export default class Step1Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      page: Page.STEP1,
    };
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.page != Page.STEP1) {
      browserHistory.push(nextProps.goal.page)
    }
  }

  getInputDomData() {
    return {name: ReactDOM.findDOMNode(this.refs.name).value.trim()}
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.validateGoal(this.getInputDomData(), Page.STEP1)
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix">

        <h1 className="goals-create-heading">{__("Choose your Goal name")}</h1>
        <p className="goals-create-description">
          {__("Your name will displayed along with your goals and posts in Goalous.")}
        </p>

        <div className="goals-create-dispaly-vision">
          <h4 className="goals-create-vision-title">
            {__("Vision : Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum")}
          </h4>
          <img className="goals-create-dispaly-vision-image" alt=""/>
          <p className="goals-create-vision-description">
            {__("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id estlaborum.")}
            <span>more</span>
          </p>
          <span className="goals-create-dispaly-vision-see-other btn"><i className="fa fa-refresh"
                                                                         ariaHidden="true"></i>{__("See Other 7")}</span>
        </div>

        <form className="goals-create-input" action="" onSubmit={(e) => this.handleSubmit(e) }>
          <label className="goals-create-input-name-label">{__("Goal name?")}</label>
          <input name="name" ref="name" className="form-control goals-create-input-name-form" type="text"
                 placeholder="e.g. Get goalous users"/>
          <InvalidMessageBox message={this.props.goal.validationErrors.name}/>

          <button type="submit" className="btn btn-primary" to="/goals/create/step2/">{__("Next")} <i
            className="fa fa-arrow-right" ariaHidden="true"></i></button>
        </form>

      </section>

    )
  }
}
