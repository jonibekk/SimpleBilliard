import React from 'react'
import { Link } from 'react-router'

export default class Auth extends React.Component {
  render() {
    return (
      <div>
        <div>auth</div>
        <div><Link to="/signup/password">password</Link></div>
      </div>
    )
  }
}
