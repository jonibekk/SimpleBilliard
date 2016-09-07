import React from 'react'
import { Link } from 'react-router'

export default class Step1Component extends React.Component {

  render() {
    return (
      <div>
        <h1>step1</h1>
        <Link to="/goals/create/step2">step2</Link>
      </div>
    )
  }
}
