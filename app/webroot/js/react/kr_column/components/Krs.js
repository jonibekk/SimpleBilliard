import React from "react";
import ReactDOM from "react-dom";

export default class Krs extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div>KRs</div>
    )
  }
}

Krs.propTypes = {
  krs: React.PropTypes.array
};
Krs.defaultProps = { krs: [] };
