import React from "react";
import axios from "axios";
import Krs from "~/kr_column/components/Krs";
import Graph from "~/kr_column/components/Graph";
import {KeyResult} from "~/common/constants/Model";
import Loading from "~/kr_column/components/Loading";
import { get } from "~/util/api"

export default class KrColumn extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      progress_graph: [],
      krs: [],
      goals: [],
      kr_count: null,
      loading_init: true,
      loading_krs: false,
      next_krs_url: ''
    }
    this.fetchKrsFilteredGoal = this.fetchKrsFilteredGoal.bind(this)
    this.fetchMoreKrs = this.fetchMoreKrs.bind(this)
  }

  componentWillMount() {
    this.fetchInitData()
  }

  /**
   * 右カラム初期表示データ取得
   * - グラフデータ
   * - KR一覧データ
   */
  fetchInitData() {
    return get(`/api/v1/goals/dashboard`, {}, (response) => {
      const data = response.data.data
      const kr_count = response.data.count
      const next_krs_url = response.data.paging.next
      this.setState({
        progress_graph: data.progress_graph,
        krs: data.krs,
        goals: data.goals,
        kr_count,
        loading_init: false,
        next_krs_url
      })
    }, (response) => {
      /* eslint-disable no-console */
      console.log(response)
      /* eslint-enable no-console */
    })
  }

  /**
   * ゴールにフィルタしたKR一覧を取得
   */
  fetchKrsFilteredGoal(goalId) {
    this.setState({
      krs: [],
      kr_count: null,
      loading_krs: true,
      next_krs_url: ''
    })
    return get(`/api/v1/goals/dashboard_krs?goal_id=${goalId || ''}`, {}, (response) => {
      const krs = response.data.data
      const kr_count = response.data.count
      const next_krs_url = response.data.paging.next
      this.setState({
        krs,
        kr_count,
        loading_krs: false,
        next_krs_url
      })
    }, (response) => {
      /* eslint-disable no-console */
      console.log(response)
      /* eslint-enable no-console */
    })
  }

  /**
   * ページングのKRデータ取得
   */
  fetchMoreKrs() {
    const next_krs_url = this.state.next_krs_url
    if (!next_krs_url) return
    this.setState({loading_krs: true})
    return get(next_krs_url, {}, (response) => {
      const krs = response.data.data
      const next_krs_url = response.data.paging.next
      this.setState({
        krs: [...this.state.krs, ...krs],
        loading_krs: false,
        next_krs_url
      })
    }, (response) => {
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
         <div className="placeholder-no-goal">
           <img src="/img/placeholder-no-goal.png" />
         </div>
      );
    }

    return (
      <div>
        <Graph progress_graph={ this.state.progress_graph } />
        <Krs krs={ this.state.krs }
             goals={ this.state.goals }
             kr_count={ this.state.kr_count }
             fetchKrsFilteredGoal={ this.fetchKrsFilteredGoal }
             fetchMoreKrs={ this.fetchMoreKrs }
             loading_krs={ this.state.loading_krs }/>
      </div>
    )
  }
}
