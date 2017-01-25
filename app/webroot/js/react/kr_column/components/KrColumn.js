import React from "react";
import axios from "axios";
import Krs from "~/kr_column/components/Krs";
import Graph from "~/kr_column/components/Graph";
import {KeyResult} from "~/common/constants/Model";
import Loading from "~/kr_column/components/Loading";

export default class KrColumn extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      progress_graph: [],
      krs: [],
      goals: {},
      kr_count: null,
      loading_init: false,
      loading_krs: false
    }
    this.fetchKrsFilteredGoal = this.fetchKrsFilteredGoal.bind(this)
  }

  componentWillMount() {
    this.fetchInitData()
  }

  fetchInitData() {
    this.setState({loading_init: true})
    return axios.get(`/api/v1/goals/dashboard?limit=${KeyResult.DASHBOARD_LIMIT}`)
      .then((response) => {
        const data = response.data.data
        const kr_count = response.data.count
        const next = response.data.paging.next
        this.setState({progress_graph: data.progress_graph})
        this.setState({krs: data.krs})
        this.setState({goals: data.goals})
        this.setState({kr_count})
        this.setState({loading_init: false})
        if (next) {
          this.fetchMoreKrs(next)
        }
      })
      .catch((response) => {
        /* eslint-disable no-console */
        console.log(response)
        /* eslint-enable no-console */
      })
  }

  fetchKrsFilteredGoal(goalId) {
    this.setState({krs: []})
    this.setState({kr_count: null})
    this.setState({loading_krs: true})
    return axios.get(`/api/v1/goals/dashboard_krs?limit=${KeyResult.DASHBOARD_LIMIT}&goal_id=${goalId || ''}`)
      .then((response) => {
        const data = response.data.data
        const kr_count = response.data.count
        const next = response.data.paging.next
        this.setState({krs: data})
        this.setState({kr_count})
        this.setState({loading_krs: false})
        if (next) {
          this.fetchMoreKrs(next)
        }
      })
      .catch((response) => {
        /* eslint-disable no-console */
        console.log(response)
        /* eslint-enable no-console */
      })
  }

  fetchMoreKrs(next) {
    this.setState({loading_krs: true})
    return axios.get(next)
      .then((response) => {
        const data = response.data.data
        const next = response.data.paging.next
        this.setState({krs: [...this.state.krs, ...data]})
        this.setState({loading_krs: false})
        if (next) {
          this.fetchMoreKrs(next)
        }
      })
      .catch((response) => {
        /* eslint-disable no-console */
        console.log(response)
        /* eslint-enable no-console */
      })
  }

  render() {
    if (this.state.loading_init) {
      return <Loading />;
    }

    if (this.state.kr_count == 0) {
      return (
        <a href="/goals/create/step1"
           className="font_gargoyleGray-brownRed btn-goals-column-plus">
          <i className="fa fa-plus-circle font_brownRed"></i>
          {__('Create a goal')}
        </a>
      );
    }

    return (
      <div>
        <Graph progress_graph={ this.state.progress_graph } />
        <Krs krs={ this.state.krs }
             goals={ this.state.goals}
             kr_count={ this.state.kr_count }
             fetchKrsFilteredGoal={ this.fetchKrsFilteredGoal }
             loading_krs={ this.state.loading_krs }/>
      </div>
    )
  }
}
