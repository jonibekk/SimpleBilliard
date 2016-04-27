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
        <div className="row">
          <div className="col-sm-12">
            <Link to="/setup/" className="btn btn-secondary setup-back-btn">Back</Link>
            <Link to="/setup/circle/select" className="btn btn-primary setup-next-btn pull-right">Next</Link>
          </div>
        </div>
      </div>
    )
  }
}
