import React from "react";
import {browserHistory} from "react-router";
import * as Page from "../constants/Page";
import InvalidMessageBox from "../../common/components/InvalidMessageBox";
import Vision from "./elements/Vision";

export default class Step1Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      visionIdx: 0,
    };
    this.handleChange = this.handleChange.bind(this)
    this.handleChangeVision = this.handleChangeVision.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.STEP1)
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

  handleChangeVision() {
    const visions = this.props.goal.visions
    const maxIdx = visions.length - 1
    const visionIdx = (maxIdx == this.state.visionIdx) ? 0 : this.state.visionIdx + 1
    this.setState({visionIdx: visionIdx})
  }

  render() {
    const {visions, validationErrors, inputData} = this.props.goal

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set your goal name")}</h1>
        <p className="goals-create-description">{__("Your name will displayed along with your goals and posts in Goalous.")}</p>

        <Vision visions={visions} visionIdx={this.state.visionIdx} onChangeVision={this.handleChangeVision} />

        <form className="goals-create-input" action onSubmit={(e) => this.handleSubmit(e) }>
          <label className="goals-create-input-label">{__("Goal name?")}</label>
          <input name="name" className="form-control goals-create-input-form" type="text"
                 placeholder="e.g. Get goalous users" ref="name"
                 onChange={this.handleChange} value={inputData.name}/>
          <InvalidMessageBox message={validationErrors.name}/>
          {/*<a href="#" className="goals-create-show-sample">*/}
            {/*<i className="fa fa-eye" aria-hidden="true"/>*/}
            {/*<span className="goals-create-interactive-link">{__("Show sample")}</span>*/}
          {/*</a>*/}

          <div className="row">
            <button type="submit" className="goals-create-btn-next btn">{__("Next →")}</button>
            <a className="goals-create-btn-cancel btn" href="/">{__("Cancel")}</a>
          </div>
        </form>
      </section>
    )
  }
}
