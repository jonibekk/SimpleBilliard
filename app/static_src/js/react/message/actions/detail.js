import * as ActionTypes from "~/message/constants/ActionTypes";
import {get, post} from "~/util/api";

export function fetchInitialData(topic_id) {
  return (dispatch) => {
    dispatch({
      type: ActionTypes.LOADING,
    })
    //ゴール検索ページでセットされたクエリパラメータをゴール検索初期化APIにそのままセット
    return get(`/api/v1/topics/${topic_id}`)
      .then((response) => {
        const data = response.data.data
        dispatch({
          type: ActionTypes.FETCH_INITIAL_DATA,
          data,
        })
      })
      .catch((response) => {
      })
  }
}
