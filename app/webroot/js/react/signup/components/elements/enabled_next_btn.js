import React from 'react'

export class EnabledNextButton extends React.Component {
  render() {
    return (
      <div className="submit signup-btn">
          <input className="btn btn-primary signup-btn-submit" type="submit" value="Next→" />
      </div>
    )
  }
}
