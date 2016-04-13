import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class Goal extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <header>
          Links:
          {' '}
          <Link to="/setup">top</Link>
          {' '}
          <Link to="/setup/goal_image">goal_image</Link>
        </header>
      </div>
    )
  }
}
