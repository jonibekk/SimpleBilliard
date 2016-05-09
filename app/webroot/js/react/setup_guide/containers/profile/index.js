import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class ProfileContainer extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        {this.props.children}
      </div>
    )
  }
}
