import React from 'react'
import { Link } from 'react-router'

export default class GoalImage extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__('Set up Goalous')} <i className="fa fa-angle-right" aria-hidden="true"></i> {__('Create a goal')}
        </div>
        <div className="setup-explain">
          <img src='/img/setup/setup_goal.png' className='setup-explain-img' alt='setup goal' />
        </div>
        <div>
          <Link to="/setup/" className="btn btn-secondary setup-back-btn">{__('Back')}</Link>
          <Link to="/setup/goal/purpose_select"
            className="btn btn-primary setup-next-btn pull-right">{__('Next')}</Link>
        </div>
      </div>
    )
  }
}
