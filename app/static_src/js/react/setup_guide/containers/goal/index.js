import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class GoalContainer extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <div>
        {this.props.children}
      </div>
    )
  }
}
