import React from 'react'
import { Link } from 'react-router'

export default class Message extends React.Component {
  render() {
    return (
      <div>
        <div>Message一覧</div>
        <Link to="/topics">Topic一覧へ</Link>
      </div>
    )
  }
}
