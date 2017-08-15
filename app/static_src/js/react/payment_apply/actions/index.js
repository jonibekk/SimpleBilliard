import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {post} from "../../util/api";
import axios from "axios";

export function validatePayment(page, add_data) {
  return (dispatch, getState) => {
    let post_data = getState().payment.input_data
    if (page == Page.COUNTRY) {
      post_data['payment_setting'] = Object.assign(post_data['payment_setting'], add_data['payment_setting']);
    }

    return post(`/api/v1/payments/validate?page=${page}`, post_data, null,
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

export function initStripe(stripe) {
  return {
    type: types.INIT_STRIPE,
    stripe
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

export function savePaymentCc(card, extra_details) {
  return (dispatch, getState) => {
    // First, validate card holder name
    if (extra_details.name == "") {
      return dispatch(invalid({
        validation_errors: {name: __("Input is required")}
      }))
    }
    dispatch(disableSubmit())
    const stripe = getState().payment.stripe
    // Request new token from Stripe then validate it
    stripe.createToken(card, extra_details).then((result) => {
      if (result.error) {
        dispatch(invalid({
          message: result.error.message,
          validation_errors:{},
        }))
      } else {
        // Send the token to your server
        const post_data = getState().payment.input_data.payment_setting
        post_data['token'] = result.token.id
        return post("/api/v1/payments/credit_card", post_data, null,
          (response) => {
            dispatch(toNextPage(Page.COMPLETE))
          },
          ({response}) => {
            // when team is in read only
            if (!response.data.validation_errors) {
              // Reason to set to validation_errors.name is that
              // This field is on to submit button
              dispatch(invalid({
                message: response.data.message
              }))
            } else {
              dispatch(invalid(response.data))
            }
          }
        );
      }
    });
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
