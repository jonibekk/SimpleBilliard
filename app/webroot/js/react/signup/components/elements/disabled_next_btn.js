import React from 'react'
import { LoadingImage } from './ajax_loader'

export class DisabledNextButton extends React.Component {
  render() {
    const ajax_loader = () => {
      return <LoadingImage />
    }

    return (
      <div className="submit signup-btn">
          { this.props.loader ? ajax_loader() : '' }
          <input className="btn btn-lightGray signup-btn-submit" type="submit" value="Next→" disabled="disabled" />
      </div>
    )
  }
}
