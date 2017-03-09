import React from 'react'
import { Link } from 'react-router'

export default class Detail extends React.Component {
  render() {
    return (
      <div>
        <div>Detail</div>
        <Link to="/topics">Indexへ</Link>
      </div>
    )
  }
}
