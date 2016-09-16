import React from 'react'

export default class InvalidMessageBox extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    if (!this.props.message) {
      return null;
    }
    return (
      <div className="has-error">
          <small className="help-block">{this.props.message}</small>
      </div>
    )
  }
}
InvalidMessageBox.propTypes = { message: React.PropTypes.string};
InvalidMessageBox.defaultProps = {message: ""};
