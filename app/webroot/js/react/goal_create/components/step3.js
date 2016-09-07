import React from 'react'
import { Link } from 'react-router'

export default class Step3Component extends React.Component {

  render() {
    return (
      <div>
        <h1>step3</h1>
        <Link to="/goals/create/step4">step4</Link>
      </div>
    )
  }
}
