import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class AppImage extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          :Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> :Login from mobile app
        </div>
        <div className="setup-explain">
            <img src='/img/setup/setup_app.png' className='setup-explain-img' alt='setup app' />
        </div>
        <Link to="/setup/app/select" className="btn btn-primary">Next</Link>
      </div>
    )
  }
}
