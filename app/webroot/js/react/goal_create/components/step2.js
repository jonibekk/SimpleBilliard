import React from 'react'
import { Link } from 'react-router'

export default class Step2Component extends React.Component {

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
          <h1 className="goals-create-heading">Choose a category and labels</h1>
          <p className="goals-create-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

          <form className="goals-create-input" action="">
              <label className="goals-create-input-category-label">Category ?</label>
              <select className="form-control goals-create-input-category-select" name="" id="">
                  <option value="a">duty</option>
                  <option value="b">growth</option>
              </select>

              <label className="goals-create-input-labels-label">Labels ?</label>
              <ul className="goals-create-input-labels-form">
                  <li className="input-labels-form-item">
                      <span className="input-labels-form-item-txt">goalous</span>
                      <Link to="#" className="input-labels-form-item-choice-close" tabIndex="-1"><i className="fa fa-times-circle" ariaHidden="true"></i></Link>
                  </li>
                  <li className="input-labels-form-item">
                      <span className="input-labels-form-item-txt">web</span>
                      <Link to="#" className="input-labels-form-item-close" tabIndex="-1"><i className="fa fa-times-circle" ariaHidden="true"></i></Link>
                  </li>
              </ul>

              <Link className="btn" to="/goals/create/step1">Back</Link>
              <Link className="btn btn-primary" to="/goals/create/step3">Next <i className="fa fa-arrow-right" ariaHidden="true"></i></Link>
          </form>

      </section>

    )
  }
}
