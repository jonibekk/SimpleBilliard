import React from 'react'
import { Link } from 'react-router'

export default class DetailComponent extends React.Component {

  componentWillMount() {
    console.log(this.props.params.goal_id)
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix">

          <h1 className="goals-approval-detial-heading">New goal created !</h1>

          <div className="goals-approval-detial-display-user">
              <img src="" alt="" />
              <p>name</p>
              <p>time</p>
          </div>

          <div className="goals-approval-detial-display-tkr">
              <img src="" alt="" />
              <p>goal name</p>

              <h4>Top KR :</h4>
              <ul>
                  <li>tKR name</li>
                  <li>tKR value</li>
                  <li>tKR Desc</li>
              </ul>
          </div>

          <Link className="btn" to="">Follow</Link>
          <Link className="btn" to="">View Deatil</Link>

          <form className="goals-approval-detial-input" action="">
              <p>Is this top KR clear ?</p>
              <Link className="btn btn-primary" to="">Yes, clear!</Link>
              <Link className="btn btn-primary" to="">Not clear...</Link>

              <div>Clear</div>
              <div>Not Clear</div>

              <textarea className="form-control goals-approval-detial-input-advice-form" name="" id="" cols="30" rows="10" placeholder="Your advice..."></textarea>
          </form>

      </section>

    )
  }
}
