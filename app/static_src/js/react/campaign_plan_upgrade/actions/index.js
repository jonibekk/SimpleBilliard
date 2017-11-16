import * as types from "../constants/ActionTypes";
import {post} from "../../util/api";
import axios from "axios";
import queryString from "query-string";

export function invalid() {
  return {
    type: types.INVALID,
  }
}

export function selectPricePlan(plan) {
  return {
    type: types.SELECT_PRICE_PLAN,
    plan
  }
}

export function fetchInitialData() {
  return (dispatch) => {
    return axios.get(`/api/v1/payments/upgrade_plan`)
      .then((response) => {
        let data = response.data.data
        dispatch({
          type: types.FETCH_INITIAL_DATA,
          data
        })
      })
      .catch((response) => {
      })
  }
}

export function upgradePricePlan(plan_code) {
  return (dispatch) => {
    dispatch({type: types.SAVING})
    // Send the token to your server
    const post_data = {plan_code};
    return post("/api/v1/payments/upgrade_plan", post_data, null,
      (response) => {
        document.location.href = '/payments'
      },
      ({response}) => {
        let err_msg = "";
        if (!response.data.validation_errors) {
          err_msg = response.data.message
        } else {
          err_msg = response.data.validation_errors.price_plan_code
        }
        new Noty({
          type: 'error',
          text: '<h4>'+cake.word.error+'</h4>'+ err_msg,
        }).show();
        dispatch(invalid())

      }
    );

  }
}

export function disableSubmit() {
  return {type: types.DISABLE_SUBMIT}
}
export function enableSubmit() {
  return {type: types.ENABLE_SUBMIT}
}

export function resetStates() {
  return (dispatch) => {
    dispatch({
      type: types.RESET_STATES
    })
  }
}

