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
          <button className="btn btn-lightGray signup-btn-submit" type="submit" disabled="disabled">{__('Next')} <i className="fa fa-angle-right"></i>
          </button>
      </div>
    )
  }
}
