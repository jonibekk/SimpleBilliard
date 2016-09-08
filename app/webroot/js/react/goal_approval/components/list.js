import React from 'react'
import { Link } from 'react-router'

export default class ListComponent extends React.Component {

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
          <div className="goals-approval-list-item">
              <img className="goals-approval-list-item-image" src="" alt="" />
              <p>goal name</p>
              <p>user name</p>
              <Link className="btn" to="/goals/approval/detail">Edit</Link>
              <Link className="btn" to="">Not to be evaluated</Link>
          </div>

      </section>

    )
  }
}
