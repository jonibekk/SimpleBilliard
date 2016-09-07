import React from 'react'
import { Link } from 'react-router'

export default class Step2Component extends React.Component {

  render() {
    return (
      <div>
        <h1>step2</h1>
        <Link to="/goals/create/step3">step3</Link>
      </div>
    )
  }
}
