import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class ProfileImage extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> Input your profile
        </div>
        <div>
            <div id="modalTutorialBox" className="setup-profile-index-image col-xxs-12">
            </div>
        </div>
        <div className="setup-items">
            <div className="submit"><input className="btn btn-lightGray pull-left" value="Skip" type="submit" /></div>
            <Link to="/setup/profile_add">
                <div className="submit"><input className="btn btn-primary pull-right setup-next-btn" value="Next" type="submit" /></div>
            </Link>
        </div>
      </div>
    )
  }
}
