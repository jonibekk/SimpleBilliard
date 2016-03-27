import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class GoalImage extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <header>
          Links:
          {' '}
          <Link to="/setup/goal">goal</Link>
        </header>
      </div>
    )
  }
}
