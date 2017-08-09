import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {KeyResult} from "~/common/constants/Model";
import {post} from "../../util/api";
import axios from "axios";

export function validatePayment(page, add_data) {
  return (dispatch, getState) => {

    const postData = Object.assign(getState().payment.input_data, add_data)
    return post(`/api/v1/payments/validate?page=${page}`, postData, null,
      (response) => {
        dispatch(toNextPage(page, add_data))
      },
      ({response}) => {
        // when team is in read only
        if (!response.data.validation_errors) {
          // Reason to set to validation_errors.name is that
          // This field is on to submit button
          dispatch(invalid({
            validation_errors: {name: response.data.message}
          }))
        } else {
          dispatch(invalid(response.data))
        }
      }
    );
  }
}

export function toNextPage(page, add_data = {}) {
  return {
    type: types.TO_NEXT_PAGE,
    page,
    add_data
  }
}

export function invalid(error) {
  return {
    type: types.INVALID,
    error
  }
}

export function updateInputData(data, key) {
  return {
    type: types.UPDATE_INPUT_DATA,
    data,
    key
  }
}

export function fetchInitialData(page) {
  const dataTypes = Page.INITIAL_DATA_TYPES[page]
  return (dispatch) => {
    return axios.get(`/api/v1/payments/init_form?data_types=${dataTypes}`)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data,
          page
        })
      })
      .catch((response) => {
      })
  }
}

export function savePaymentSetting() {
  return (dispatch, getState) => {
    dispatch(disableSubmit())
    const postData = getState().payment.input_data
    if (postData.key_result.value_unit == KeyResult.ValueUnit.NONE) {
      postData.key_result.start_value = 0
      postData.key_result.target_value = 1
    }
    return post("/api/v1/goals", getState().payment.input_data, null,
      (response) => {
        // 成功時はリダイレクト
        dispatch({type: types.REDIRECT_TO_HOME})
      },
      ({response}) => {
        dispatch(invalid(response.data))
      }
    );
  }
}

export function disableSubmit() {
  return {type: types.DISABLE_SUBMIT}
}
export function resetStates() {
  return (dispatch) => {
    dispatch({
      type: types.RESET_STATES
    })
  }
}
