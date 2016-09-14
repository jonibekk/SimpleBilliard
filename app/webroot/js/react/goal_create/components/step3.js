import React from "react";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
// import CategorySelect from "./elements/CategorySelect";
import InvalidMessageBox from "./elements/InvalidMessageBox";

export default class Step3Component extends React.Component {

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">Set your goal image</h1>
        <p className="goals-create-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
          tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
          laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit
          esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui
          officia deserunt mollit anim id est laborum.</p>
        <form className="goals-create-input" action encType="multipart/form-data">
          <label className="goals-create-input-label">Goal image?</label>
          <div className="goals-create-input-image-upload">
            <img className="goals-create-input-image-upload-preview" src alt width={100} height={100}/>
            <div className="goals-create-input-image-upload-info">
              <p className="goals-create-input-image-upload-info-text">This is sample image if you want to upload your
                original image</p>
              <label className="goals-create-input-image-upload-info-link" htmlFor="file_photo">
                Upload a image
                <input className="goals-create-input-image-upload-info-form" type="file" name id="file_photo"/>
              </label>
            </div>
          </div>
          <label className="goals-create-input-label">Term?</label>
          <select className="form-control goals-create-input-form mod-select" name id>
            <option value>This Term(Apr 1, 2016 - Sep 30, 2016)</option>
            <option value>Next Term(Oct 1, 2016 - Mar 31, 2016)</option>
          </select>
          <a className="goals-create-view-more" href><i className="fa fa-eye" aria-hidden="true"/> <span
            className="goals-create-interactive-link">View more options</span></a>
          <label className="goals-create-input-label">Description</label>
          <textarea className="goals-create-input-form mod-textarea" name id defaultValue={""}/>
          <label className="goals-create-input-label">End date</label>
          <input className="goals-create-input-form" type="date"/>
          <label className="goals-create-input-label">Weight</label>
          <select className="goals-create-input-form mod-select" name id>
            <option value={0}>0 (認定対象外)</option>
            <option value={1}>1 (とても低い)</option>
            <option value={2}>2</option>
            <option value={3}>3 (デフォルト)</option>
            <option value={4}>4</option>
            <option value={5}>5 (とても高い)</option>
          </select>
          <a className="goals-create-btn-next btn" href="/goals/create/step4/gucchi">Next →</a>
          <Link className="goals-create-btn-cancel btn" to={Page.STEP2}>{__("Back")}</Link>
        </form>
      </section>

    )
  }
}
