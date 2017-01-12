import React from "react";
import axios from "axios";
import Graph from '~/kr_column/components/Graph'
import Krs from '~/kr_column/components/Krs'
import { KeyResult } from "~/common/constants/Model";

export default class KrColumn extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      progress_graph: [],
      krs: []
    }
  }

  componentWillMount() {
    this.fetchInitData()
  }

  fetchInitData() {
    return axios.get(`/api/v1/goals/dashboard?limit=${KeyResult.DASHBOARD_LIMIT}`)
      .then((response) => {
        const data = response.data.data
        this.setState({ progress_graph: data.progress_graph })
        this.setState({ krs: data.krs })
      })
      .catch((response) => {
        /* eslint-disable no-console */
        console.log(response)
        /* eslint-enable no-console */
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
