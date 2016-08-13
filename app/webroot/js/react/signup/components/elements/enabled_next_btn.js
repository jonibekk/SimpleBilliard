import React from 'react'

export class EnabledNextButton extends React.Component {
  render() {
    return (
      <div className="submit signup-btn">
          <button className="btn btn-primary signup-btn-submit" type="submit">
              {__('Next')} <i className="fa fa-angle-right"></i>
          </button>
      </div>
    )
  }
}
