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
    return axios.get('/api/v1/goals/kr_column')
      .then((response) => {
        let data = response.data.data
        this.setState({ progress_logs: data.progress_logs });
        this.setState({ krs: data.krs });
      })
      .catch((response) => {
        console.log(response)
      })
  }

  render() {
    return (
      <div>
        <Graph progress_logs={ this.state.progress_logs } />
        <Krs krs={ this.state.krs } />
      </div>
    )
  }
}
