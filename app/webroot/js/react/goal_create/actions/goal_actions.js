import * as types from "../constants/ActionTypes";
import {post} from "./common_actions";
import axios from "axios";

export function validateGoal(data) {
  return dispatch => {
    return post('/api/v1/goals/validate', data, null,
      response => {
        console.log("validate success");
        dispatch(toNextPage())
      }
      // response => {
      //   console.log("validate failed");
      //   dispatch(invalid(response.data))
      // }
    );
  }
}

export function toNextPage() {
  return {
    type: types.TO_NEXT_PAGE
  }
}

export function invalid(error) {
  return {
    type: types.INVALID,
    error: error
  }
}

export function fetchInitialData(dispatch) {
  return dispatch => {
    return axios.get('/api/v1/goals/init_form?data_type=categories,labels',
      {
        timeout: 10000,
      })
      .then(response => {
        console.log("fetchInitialData success")
        console.log(response)
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data: response.data.data,
        })
      })
      .catch(response => {
      })
  }
}
