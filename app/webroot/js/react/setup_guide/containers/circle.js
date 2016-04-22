import React from 'react'
import { Link, browserHistory } from 'react-router'
import * as actionCreators from '../actions/circle_actions'
import { bindActionCreators } from 'redux'

class CircleContainer extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <div>
        {this.props.children}
      </div>
    )
  }
}

function mapStateToProps(state) {
  return { circle: state.circle }
}

function mapDispatchToProps(dispatch) {
  return { actions: bindActionCreators({ actionCreators }, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(CircleContainer);
