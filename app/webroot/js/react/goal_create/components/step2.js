import React from "react";
import {Link} from "react-router";
import ReactDOM from "react-dom";
import * as Page from "../constants/Page";
import CategorySelect from "./elements/CategorySelect";
import InvalidMessageBox from "./elements/InvalidMessageBox";


export default class Step2Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      page: Page.STEP2,
      validation_errors: {},
      categories: [],
      labels: []
    };
  }

  componentWillMount() {
    this.props.fetchInitialData()
  }


  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.page != Page.STEP2) {
      browserHistory.push(nextProps.goal.page)
    }
  }

  getInputDomData() {
    return {
      value_unit: ReactDOM.findDOMNode(this.refs.category).value,
    }
  }

  handleSubmit(e) {
    console.log("step2 handleSubmit")
    e.preventDefault()
    this.props.validateGoal(this.getInputDomData())
  }

  render() {
    console.log("step2 render")
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
        <h1 className="goals-create-heading">{__("Choose a category and labels")}</h1>
        <p className="goals-create-description">
          {__("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.")}
        </p>

        <form className="goals-create-input" action="" onSubmit={(e) => this.handleSubmit(e) }>
          <label className="goals-create-input-category-label">{__("Category")}</label>
          <CategorySelect categories={this.props.goal.categories}/>

          <label className="goals-create-input-labels-label">{__("Labels")}</label>
          <ul className="goals-create-input-labels-form">
            <li className="input-labels-form-item">
              <span className="input-labels-form-item-txt">goalous</span>
              <Link to="#" className="input-labels-form-item-choice-close" tabIndex="-1"><i
                className="fa fa-times-circle" ariaHidden="true"></i></Link>
            </li>
            <li className="input-labels-form-item">
              <span className="input-labels-form-item-txt">web</span>
              <Link to="#" className="input-labels-form-item-close" tabIndex="-1"><i className="fa fa-times-circle"
                                                                                     ariaHidden="true"></i></Link>
            </li>
          </ul>

          <Link className="btn" to={Page.STEP1}>{__("Back")}</Link>
          <button type="submit" className="btn btn-primary">
            {__("Next")} <i className="fa fa-arrow-right" ariaHidden="true"></i>
          </button>
        </form>

      </section>

    )
  }
}
