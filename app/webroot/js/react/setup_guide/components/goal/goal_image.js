import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class GoalImage extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          :Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> :Create a goal
        </div>
        <div className="setup-explain">
            <img src='/img/setup/goal.png' className='setup-explain-img' alt='setup goal' />
        </div>
        <Link to="/setup/goal/purpose_select" className="btn btn-primary">Next</Link>
      </div>
    )
  }
}
