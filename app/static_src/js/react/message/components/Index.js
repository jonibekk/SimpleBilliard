import React from 'react'
import { Link } from 'react-router'

export default class Index extends React.Component {
  render() {
    return (
      <div>
        <div>Index</div>
        <Link to="/topics/1/detail">Detailへ</Link>
      </div>
    )
  }
}
