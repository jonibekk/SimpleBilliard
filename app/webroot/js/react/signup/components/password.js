import React from 'react'
import { Link } from 'react-router'

export default class Password extends React.Component {
  render() {
    return (
      <div>
        <div>password</div>
        <div><Link to="/signup/user">user</Link></div>
      </div>
    )
  }
}
