import React from "react";
import ReactDOM from "react-dom";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import PhotoUpload from "~/common/components/goal/PhotoUpload";
import InvalidMessageBox from "../../common/components/InvalidMessageBox";


export default class Step3Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      showMoreOption: false
    };
    this.handleChange = this.handleChange.bind(this);
    this.handleClick = this.handleClick.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.STEP3)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      browserHistory.push(Page.URL_STEP4)
    }
  }

  getInputDomData() {
    const photoNode = this.refs.innerPhoto.refs.photo
    const photo = ReactDOM.findDOMNode(photoNode).files[0]
    if (!photo) {
      return {}
    }
    return {photo}
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.validateGoal(Page.STEP3, this.getInputDomData())
  }

  handleChange(e) {
    let data = {[e.target.name]: e.target.value}
    // 評価期間の選択によって自動的にゴール終了日を切り替える
    if (e.target.name == "term_type") {
      data["end_date"] = this.props.goal.default_end_dates[e.target.value]
    }
    this.props.updateInputData(data)
  }

  handleClick(e) {
    e.preventDefault()
    this.setState({showMoreOption: true})
  }

  render() {
    const showMoreLinkClass = "goals-create-view-more " + (this.state.showMoreOption ? "hidden" : "");

    const {inputData, priorities, validationErrors} = this.props.goal;
    let priorityOptions = null;
    if (priorities.length > 0) {
      priorityOptions = priorities.map((v) => {
        return <option key={v.id} value={v.id}>{v.label}</option>
      });
    }
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set goal details")}</h1>
        <p
          className="goals-create-description">{__("Customize your goal using the below options.")}</p>
        <form className="goals-create-input"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              onSubmit={(e) => this.handleSubmit(e)}>

          <PhotoUpload uploadPhoto={inputData.photo} ref="innerPhoto"/>
          <InvalidMessageBox message={validationErrors.photo}/>

          <label className="goals-create-input-label">{__("Term")}</label>
          <select name="term_type" className="form-control goals-create-input-form mod-select" ref="term_type"
                  value={inputData.term_type} onChange={this.handleChange}>
            <option value="current">{__("This Term")}</option>
            <option value="next">{__("Next Term")}</option>
          </select>
          <InvalidMessageBox message={validationErrors.term_type}/>

          <a className={showMoreLinkClass} href="#" onClick={this.handleClick}>
            <i className="fa fa-eye" aria-hidden="true"/>
            <span className="goals-create-interactive-link">
              {__("View more options")}
              </span>
          </a>
          <div className={this.state.showMoreOption ? "" : "hidden"}>
            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea className="goals-create-input-form mod-textarea" name="description" onChange={this.handleChange} value={inputData.description}/>
            <InvalidMessageBox message={validationErrors.description}/>

            <label className="goals-create-input-label">{__("End date")}</label>
            <input className="goals-create-input-form" type="date" name="end_date" onChange={this.handleChange} value={inputData.end_date} />
            <InvalidMessageBox message={validationErrors.end_date}/>
            <label className="goals-create-input-label">{__("Weight")}</label>
            <select className="goals-create-input-form mod-select" name="priority" ref="priority"
                    value={inputData.priority} onChange={this.handleChange}>
              {priorityOptions}
            </select>
            <InvalidMessageBox message={validationErrors.priority}/>
          </div>
          <button type="submit" className="goals-create-btn-next btn">{__("Next →")}</button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP2}>{__("Back")}</Link>
        </form>
      </section>

    )
  }
}
