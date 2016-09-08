import React from 'react'
import { Link } from 'react-router'

export default class Step4Component extends React.Component {

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix">

          <h1 className="goals-create-heading">Choose Key Result</h1>
          <p className="goals-create-description">Set measurable target to achieve your goal.</p>

          <form className="goals-create-input" action="">
              <label className="goals-create-input-tkr-label">Key Result name?</label>
              <p>your top key result is required.</p>

              <input className="form-control goals-create-input-tkr-name-form" type="text" placeholder="key result name" />
              <input className="form-control goals-create-input-tkr-weight-form" type="text" defaultValue="Highest" />

              <select className="form-control goals-create-input-tkr-range-unit-form" name="" id="">
                  <option value="%">%</option>
                  <option value="円">円</option>
              </select>
              <input className="form-control goals-create-input-tkr-range-from-form" type="text" />
              <input className="form-control goals-create-input-tkr-range-to-form" type="text" />

              <Link to="">Add description</Link>
              <textarea className="form-control goals-create-input-tkr-description-form" name="" id="" cols="30" rows="10"></textarea>

              <Link className="btn" to="/goals/create/step3">Back</Link>
              <Link className="btn btn-primary" to="/goals/approval/list">Save and share</Link>
          </form>

      </section>

    )
  }
}
