import React from "react";
import ReactDOM from "react-dom";
import axios from "axios";
import Graph from '~/kr_column/components/Graph'
import Krs from '~/kr_column/components/Krs'

export default class KrColumn extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      progress_logs: [],
      krs: []
    }
  }

  componentWillMount() {
    this.fetchInitData()
  }

  fetchInitData() {
    return axios.get('/api/v1/goals/dashboard')
      .then((response) => {
        const data = response.data.data
        this.setState({ progress_graph: data.progress_graph });
        this.setState({ krs: data.krs });
      })
      .catch((response) => {
        console.log(response)
      })
  }

  render() {
    return (
      <div>
        <Graph progress_graph={ this.state.progress_graph } />
        <Krs krs={ this.state.krs } />
      </div>
    )
  }
}
