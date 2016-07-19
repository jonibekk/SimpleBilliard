import React from 'react'
import { Link } from 'react-router'

export default class UserName extends React.Component {
  render() {
    return (
      <div>
        <div>user</div>
        <div><Link to="/signup/team">team</Link></div>
      </div>
    )
  }
}
