import React from 'react'

export default class InvalidMessageBox extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <div className="has-error">
          <small className="help-block">{ (this.props.message) ? this.props.message : '' }</small>
      </div>
    )
  }
}
// InvalidMessageBox.propTypes = { message: React.PropTypes.string};
// InvalidMessageBox.defaultProps = {message: ""};
