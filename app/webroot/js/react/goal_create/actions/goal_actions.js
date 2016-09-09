import * as types from "../constants/ActionTypes";
import {post} from "./common_actions";

export function validateGoal(data) {
  return dispatch => {
    post('/api/v1/goals/validates', data, null,
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
