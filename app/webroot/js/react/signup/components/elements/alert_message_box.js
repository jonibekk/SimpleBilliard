import React from 'react'

export class AlertMessageBox extends React.Component {
  render() {
    return (
      <div className="signup-error-description">
          <i className="fa fa-exclamation-circle signup-load-icon mod-error"></i> {this.props.message}
      </div>
    )
  }
}
