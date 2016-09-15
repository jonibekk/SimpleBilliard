import React from "react";
import ReactDOM from "react-dom";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";

export default class Step3Component extends React.Component {
  constructor(props) {
    super(props);
    this.handleChange = this.handleChange.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.STEP3)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      browserHistory.push(Page.URL_STEP3)
    }
  }

  getInputDomData() {
    return {
      photo: ReactDOM.findDOMNode(this.refs.photo).files[0]
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    // ゴール保存
    // this.props.saveGoal("")
  }

  handleDocumentTitleChange(e) {
    e.preventDefault()
    // ゴール保存
    console.log("handleDocumentTitleChange")
  }

  handleChange(e) {
    this.props.updateInputData({[e.target.name]: e.target.value})
  }

  render() {

    console.log("step3 render")
    console.log(this.props.goal.inputData)
    const imgPath = this.props.goal.inputData.image || "/img/no-image-goal.jpg";
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set your goal image")}</h1>
        <p
          className="goals-create-description">{__("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmodtempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamcolaboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velitesse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa quiofficia deserunt mollit anim id est laborum.")}</p>
        <form className="goals-create-input" action encType="multipart/form-data"
              onSubmit={(e) => this.handleSubmit(e)}>
          <label className="goals-create-input-label">{__("Goal image?")}</label>
          <div className="goals-create-input-image-upload fileinput_small fileinput-new" data-provides="fileinput">
            <div className="fileinput-preview thumbnail nailthumb-container photo-design" data-trigger="fileinput">
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
          <label className="goals-create-input-label">{__("Term?")}</label>
          <select className="form-control goals-create-input-form mod-select" name="term_type" onChange={this.handleChange}>
            <option value>{__("This Term(Apr 1, 2016 - Sep 30, 2016)")}</option>
            <option value>{__("Next Term(Oct 1, 2016 - Mar 31, 2016)")}</option>
          </select>
          <a className="goals-create-view-more" href><i className="fa fa-eye" aria-hidden="true"/> <span
            className="goals-create-interactive-link">{__("View more options")}</span></a>
          <label className="goals-create-input-label">{__("Description")}</label>
          <textarea className="goals-create-input-form mod-textarea" name="description" onChange={this.handleChange}
                    defaultValue={""}/>
          <label className="goals-create-input-label">{__("End date")}</label>
          <input className="goals-create-input-form" type="date"  name="end_date" onChange={this.handleChange}/>
          <label className="goals-create-input-label">{__("Weight")}</label>
          <select className="goals-create-input-form mod-select" name="weight" onChange={this.handleChange}>
            <option value={0}>{__("0(not affect the progress)")}</option>
            <option value={1}>{__("1(Very low)")}</option>
            <option value={2}>{__("2")}</option>
            <option value={3}>{__("3(default)")}</option>
            <option value={4}>{__("4")}</option>
            <option value={5}>{__("5(Very high)")}</option>
          </select>
          <a className="goals-create-btn-next btn" href="/goals/create/step4/gucchi">{__("Next →")}</a>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP2}>{__("Back")}</Link>
        </form>
      </section>

    )
  }
}
