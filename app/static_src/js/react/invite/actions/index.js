import * as types from "../constants/ActionTypes";
import {post} from "../../util/api";
import axios from "axios";

export function validateInvitation() {
  return (dispatch, getState) => {

    const post_data = getState().invite.input_data
    return post(`/api/v1/invitations/validate`, post_data, null,
      (response) => {
        /* eslint-disable no-console */
        console.log("validate success");
        /* eslint-enable no-console */
        dispatch(toNextPage(response.data.data))
      },
      ({response}) => {
        /* eslint-disable no-console */
        console.log("validate failed");
        /* eslint-enable no-console */
        dispatch(invalid(response.data))
      }
    );
  }
}

export function toNextPage(data) {
  return {
    type: types.TO_NEXT_PAGE,
    data: data
  }
}

export function invalid(error) {
  return {
    type: types.INVALID,
    error
  }
}

export function updateInputData(input_data) {
  return {
    type: types.UPDATE_INPUT_DATA,
    input_data
  }
}

export function fetchInputInitialData() {
  return (dispatch) => {
    return axios.get(`/api/v1/invitations/input`)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: types.FETCH_INPUT_INITIAL_DATA,
          data,
        })
      })
      .catch((response) => {
      })
  }
}

export function fetchConfirmInitialData() {
  return (dispatch, getState) => {
    const invitations_count = getState().invite.emails.length;
    return axios.get(`/api/v1/invitations/confirm?invitation_count=${invitations_count}`)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: types.FETCH_CONFIRM_INITIAL_DATA,
          data,
        })
      })
      .catch((response) => {
      })
  }
}

export function saveInvitation() {
  return (dispatch, getState) => {
    dispatch(disableSubmit())
    const post_data = {
      emails: getState().invite.emails
    }
    return post("/api/v1/invitations", post_data, null,
      (response) => {
        dispatch({type: types.REDIRECT_TO_HOME})
      },
      ({response}) => {
        dispatch(invalid(response.data))
      }
    );
  }
}
