import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class Index extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div className="setup-container col col-sm-8 col-sm-offset-2 panel">
        <div className="setup-inner col col-xxs-10 col-xxs-offset-1 pb_8px pt_20px font_verydark">
          {this.props.children}
        </div>
      </div>
    )
  }
}
