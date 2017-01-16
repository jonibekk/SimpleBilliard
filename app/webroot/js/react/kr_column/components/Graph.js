import React from "react";
import ReactDOM from "react-dom";

export default class Graph extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div></div>
    )
  }
}

Graph.propTypes = {
  progress_graph: React.PropTypes.array
};
Graph.defaultProps = { progress_graph: [] };
