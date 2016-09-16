import React from "react";
import {browserHistory, Link} from "react-router";
import * as Page from "../constants/Page";
import InvalidMessageBox from "./elements/InvalidMessageBox";

export default class Step4Component extends React.Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    // this.props.fetchInitialData(Page.STEP4)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      // 登録完了後はTOPにリダイレクト
      location.href = "/"
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    // ゴール・tKR登録
    // this.props.saveGoal()
  }

  handleChange(e) {
    this.props.updateInputData({key_result:{[e.target.name]: e.target.value}})
  }

  handleClick(e) {
    e.preventDefault()
    this.setState({showMoreOption: true})
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Set a top key result (tKR)")}</h1>
        <p className="goals-create-description">{__("Set measurable target to achieve your goal.")}</p>
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e)}>
          {/*        <label class="goals-create-input-label">{__("Key Result name?")}</label>
           <p class="goals-create-input-label-discription">{__("your top key result is required.")}</p>
           */}
          <label htmlFor>{__("tKR name")}</label>
          <input name="name" className="form-control goals-create-input-form goals-create-input-form-tkr-name" type="text" placeholder="e.g. Increase monthly active users" onChange={this.handleChange}/>
          <label htmlFor>{__("Unit & Range")}</label>
          <select name="unit" className="form-control goals-create-input-form goals-create-input-form-tkr-range-unit mod-select">
            <option value={0}>{__("%")}</option>
            <option value={3}>{__("¥")}</option>
            <option value={4}>{__("$")}</option>
            <option value={1}>{__("その他の単位")}</option>
            <option value={2}>{__("なし")}</option>
          </select>
          <div className="goals-create-layout-flex">
            <input name="start_value" className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text" placeholder={0} />
            <span className="goals-create-input-form-tkr-range-symbol">&gt;</span>
            <input name="target_value" className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text" placeholder={100} />
          </div>
          <a href>
            <i className="fa fa-plus-circle" aria-hidden="true" />
            <span className="goals-create-interactive-link">{__("Add description")}</span>
          </a>
          <textarea name="description" className="form-control goals-create-input-form tkr-description" cols={30} rows={10} defaultValue={""} />
          <button type="submit" className="goals-create-btn-next btn" >{__("Save and share")}</button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP3}>{__("Back")}</Link>
        </form>
      </section>

    )
  }
}
