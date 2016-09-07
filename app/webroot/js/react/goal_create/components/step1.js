import React from 'react'
import { Link } from 'react-router'

export default class Step1Component extends React.Component {

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix">

          <h1 className="goals-create-heading">Choose your Goal name</h1>
          <p className="goals-create-description">Your name will displayed along with your goals and posts in Goalous.</p>

          <div className="goals-create-dispaly-vision">
              <h4 className="goals-create-vision-title">Vision : Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</h4>
              <img className="goals-create-dispaly-vision-image" alt="" />
              <p className="goals-create-vision-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<span>more</span></p>
              <span className="goals-create-dispaly-vision-see-other btn"><i className="fa fa-refresh" ariaHidden="true"></i> See Other 7</span>
          </div>

          <form className="goals-create-input" action="">
              <label className="goals-create-input-name-label">Goal name?</label>
              <input className="form-control goals-create-input-name-form" type="text" placeholder="e.g. Get goalous users" />

              <Link className="btn btn-primary" to="/goals/create/step2/">Next <i className="fa fa-arrow-right" ariaHidden="true"></i></Link>
          </form>

      </section>

    )
  }
}
