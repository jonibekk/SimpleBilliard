import React from 'react'
import { Link } from 'react-router'

export default class AppContainer extends React.Component {
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
