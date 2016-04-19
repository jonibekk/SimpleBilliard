import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class CircleImage extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> Join a circle
        </div>
        <div className="setup-explain">
            <img src='/img/setup/circle.png' className='setup-explain-img' alt='setup goalous' />
        </div>
        <Link to="/setup/circle/select" className="btn btn-primary">Next</Link>
      </div>
    )
  }
}
