import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
import {PaymentSetting} from "~/common/constants/Model";

const initial_state = {
  to_next_page: false,
  validation_errors: {
  },
  countries: {},
  lang_code: "",
  input_data: {
    payment_setting: {
      payment_type: PaymentSetting.PAYMENT_TYPE.CREDIT_CARD,
      company_country: "",
      company_post_code: "",
      company_region: "",
      company_city: "",
      company_street: "",
      contact_mail: "",
      company_tel: "",
    },
    credit_card: {
      customer_code: ""
    },
    invoice: {
      //TODO:add
    }
  },
  is_disabled_submit: false,
  redirect_to_home: false
}

export default function payment(state = initial_state, action) {
  let input_data = state.input_data
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, {
        validation_errors: action.error.validation_errors,
        is_disabled_submit: false
      })
    case types.DISABLE_SUBMIT:
      return Object.assign({}, state, {
        is_disabled_submit: true
      })
    case types.TO_NEXT_PAGE:
      input_data = updateInputData(input_data, action.page, action.add_data);
      // input_data = Object.assign({}, input_data, action.add_data)
      return Object.assign({}, state, {
        input_data,
        to_next_page: true,
        validation_errors: {}
      })
    case types.FETCH_INITIAL_DATA:
      return Object.assign({}, state, action.data, {
        to_next_page: false,
        validation_errors: {}
      })
    case types.UPDATE_INPUT_DATA:
      if (action.key) {
        // 多次元配列のマージの場合Object.assignでバグが発生するので以下のように処理
        // TODO:配列マージ用の共通関数を作成
        input_data[action.key] = input_data[action.key] || {}
        input_data[action.key] = Object.assign({}, input_data[action.key], action.data)
        state.input_data = input_data
        return Object.assign({}, state)
      } else {
        input_data = Object.assign({}, input_data, action.data)
        return Object.assign({}, state, {
          input_data
        })
      }
    case types.RESET_STATES:
      return Object.assign({}, state, {
        to_next_page: false,
        validation_errors: {}
      });

    case types.REDIRECT_TO_HOME:
      return Object.assign({}, state, {
        redirect_to_home: true
      })
    default:
      return state;
  }
}

/**
 * Set new daeta to input data by page
 *
 * @param input_data
 * @param page
 * @param add_data
 * @returns {*}
 */
export function updateInputData(input_data, page, add_data) {
  switch (page) {
    case Page.COUNTY:
      input_data["payment_setting"] = Object.assign({}, input_data["payment_setting"], add_data);
      break;
    case Page.COMPANY:
      break;
    case Page.CREDIT_CARD:
      break;
    default:
      return input_data;
  }
  return input_data;
}
