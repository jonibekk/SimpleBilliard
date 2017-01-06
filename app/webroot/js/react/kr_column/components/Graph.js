import React from "react";
import ReactDOM from "react-dom";

export default class Graph extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div>Graph</div>
    )
  }
}

Graph.propTypes = {
  progress_logs: React.PropTypes.array
};
Graph.defaultProps = { progress_logs: [] };
