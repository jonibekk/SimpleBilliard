import React from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class CircleSelect extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  onSubmitJoin(e) {
    e.preventDefault()
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> Create a goal
        </div>
        <div className="setup-items">
          <Link to="/setup/goal/create" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="setup-items-select-circle">
            News
            </div>
          </Link>

          <Link to="/setup/goal/create" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="setup-items-select-circle">
            Gourmet
            </div>
          </Link>

          <Link to="/setup/goal/create" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="setup-items-select-circle">
            President room
            </div>
          </Link>
        </div>
        <Link to="/setup/" className="btn btn-secondary setup-back-btn">Back</Link>
        <Link to="/setup/circle/select" className="btn btn-primary setup-next-btn pull-right">Join a circle</Link>
      </div>
    )
  }
}
