import React from 'react'
import { Link } from 'react-router'

export default class Topic extends React.Component {
  render() {
    return (
      <div>
        <div>Topic一覧</div>
        <Link to="/topics/1">Message</Link>
      </div>
    )
  }
}
