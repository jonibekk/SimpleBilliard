import * as types from "../constants/ActionTypes";
import * as Page from "../constants/Page";
// import {KeyResult} from "~/common/constants/Model";

const initialState = {
  to_next_page: false,
  validation_errors: {
    key_result: {}
  },
  countries: {},
  lang_code: "",
  input_data: {
    country: "",
  },
  is_disabled_submit: false,
  can_approve: false,
  redirect_to_home: false
}

export default function payment(state = initialState, action) {
  let input_data = state.input_data
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, {
        validation_errors: action.error.validation_errors,
        is_disabled_submit: false
      })
    case types.MOVE_PAGE_BY_PAYMENT_TYPE:
      return Object.assign({}, state, {
        validation_errors: action.error.validation_errors,
        is_disabled_submit: false
      })
    case types.DISABLE_SUBMIT:
      return Object.assign({}, state, {
        is_disabled_submit: true
      })
    case types.TO_NEXT_PAGE:
      input_data = Object.assign({}, input_data, action.addData)
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
      }
      {
        input_data = Object.assign({}, input_data, action.data)
        return Object.assign({}, state, {
          input_data
        })
      }
    case types.REDIRECT_TO_HOME:
      return Object.assign({}, state, {
        redirect_to_home: true
      })
    default:
      return state;
  }
}

/**
 * 画面初期化に伴う入力値初期化
 * 既に行っている場合は不要
 * @param input_data
 * @param page
 * @param data
 * @returns {{}}
 */
export function initInputData(input_data, page, data) {
  switch (page) {
    case Page.COMPANY:
      if (!input_data.goal_category_id && data.categories.length > 0) {
        input_data["goal_category_id"] = data.categories[0].id
      }
      break;
    case Page.CREDIT_CARD:
      input_data["term_type"] = 'current'
      if (!input_data.priority && data.priorities.length > 0) {
        input_data["priority"] = KeyResult.Priority.DEFAULT;
      }
      if (!input_data.end_date && Object.keys(data.default_end_dates).length > 0) {
        input_data["end_date"] = data.default_end_dates.current
      }
      break;
    case Page.COMPLETE:
      if (Object.keys(input_data.key_result).length > 0 && input_data.key_result.value_unit) {
        break;
      }
      input_data["key_result"] = input_data.key_result || {};

      if (data.units.length > 0) {
        input_data.key_result["value_unit"] = data.units[0].id
      }
      input_data.key_result["start_value"] = 0
      input_data.key_result["target_value"] = 100

      break;
    default:
      return input_data;
  }
  return input_data;
}
