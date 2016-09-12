import * as types from "../constants/ActionTypes";
import {post} from "./common_actions";

export function validateGoal(data) {
  return dispatch => {
    post('/api/v1/goals/validate', data, null,
      response => {
        dispatch(toNextPage())
      },
      response => {
        dispatch(invalid(response.data))
      }
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
  axios.get('/api/v1/goals/initial_form?data_type=categories,labels', {
    timeout: 10000,
  })
    .then(response => {
      dispatch({
        type: types.FETCH_INITIAL_DATA,
        data: response.data,
      })
    })
    .catch(response => {
  })
}
