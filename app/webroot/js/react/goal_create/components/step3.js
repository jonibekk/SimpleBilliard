import React from "react";
import ReactDOM from "react-dom";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import InvalidMessageBox from "./elements/InvalidMessageBox";

export default class Step3Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      showMoreOption: false
    }
    this.handleChange = this.handleChange.bind(this)
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
    return {
      photo: ReactDOM.findDOMNode(this.refs.photo).files[0]
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.validateGoal(Page.STEP3, this.getInputDomData())
  }

  handleChange(e) {
    this.props.updateInputData({[e.target.name]: e.target.value})
  }

  handleClick(e) {
    e.preventDefault()
    this.setState({showMoreOption: true})
  }

  render() {
    // TODO:画面遷移を行うとイベントが発火しなくなる為、コード追加(既存バグ)
    // 将来的に廃止
    $('.fileinput_small').fileinput().on('change.bs.fileinput', function () {
      $(this).children('.nailthumb-container').nailthumb({width: 96, height: 96, fitDirection: 'center center'});
    });

    const imgPath = this.props.goal.inputData.image || "/img/no-image-goal.jpg"
    const showMoreLinkClass = "goals-create-view-more " + (this.state.showMoreOption ? "hidden" : "")

    const {priorities, validationErrors} = this.props.goal
    let priorityOptions = null;
    if (this.props.goal.priorities.length > 0) {
      priorityOptions = this.props.goal.priorities.map((v, k) => {
        return <option key={k} value={k}>{v}</option>
      });
    }

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set your goal image")}</h1>
        <p
          className="goals-create-description">{__("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmodtempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamcolaboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velitesse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa quiofficia deserunt mollit anim id est laborum.")}</p>
        <form className="goals-create-input"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              onSubmit={(e) => this.handleSubmit(e)}>
          <label className="goals-create-input-label">{__("Goal image?")}</label>
          <div className="goals-create-input-image-upload fileinput_small fileinput-new" data-provides="fileinput">
            <div
              className="fileinput-preview thumbnail nailthumb-container photo-design goals-create-input-image-upload-preview"
              data-trigger="fileinput">
              <img src={imgPath} width={100} height={100}/>
            </div>
            <div className="goals-create-input-image-upload-info">
              <p className="goals-create-input-image-upload-info-text">This is sample image if you want to upload your
                original image</p>
              <label className="goals-create-input-image-upload-info-link " htmlFor="file_photo">
                <span className="fileinput-new">{__("Upload a image")}</span>
                <span className="fileinput-exists">{__("Reselect an image")}</span>
                <input className="goals-create-input-image-upload-info-form" type="file" name="photo" ref="photo"
                       id="file_photo"/>
              </label>
            </div>
          </div>
          <InvalidMessageBox message={validationErrors.photo}/>
          <label className="goals-create-input-label">{__("Term?")}</label>
          <select className="form-control goals-create-input-form mod-select" name="term_type" ref="term_type"
                  onChange={this.handleChange}>
            <option value="current">{__("Current Term")}</option>
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
            <textarea className="goals-create-input-form mod-textarea" name="description" onChange={this.handleChange}/>
            <InvalidMessageBox message={validationErrors.description}/>

            <label className="goals-create-input-label">{__("End date")}</label>
            <input className="goals-create-input-form" type="date" name="end_date" onChange={this.handleChange}/>
            <InvalidMessageBox message={validationErrors.end_date}/>
            <label className="goals-create-input-label">{__("Weight")}</label>
            <select className="goals-create-input-form mod-select" name="priority" ref="priority"
                    onChange={this.handleChange}>
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
