import React from 'react'
import { Link, browserHistory } from 'react-router'
import Top from '../components/top'

export default class TopContainer extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <Top />
    )
  }
}
