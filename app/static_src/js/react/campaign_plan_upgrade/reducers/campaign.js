import * as types from "../constants/ActionTypes";
import {PaymentSetting} from "~/common/constants/Model";

const initial_state = {
  price_plans: [],
  charge_users_count: 0,
  selected_campaign_plan_code: "",
  current_price_plan_code: "",
  current_price_plan: {},
  is_saving: false,
  selected_price_plan: {},
}

export default function campaign(state = initial_state, action) {
  switch (action.type) {
    case types.INVALID:
      return Object.assign({}, state, {
        is_saving: false
      })
    case types.SAVING:
      return Object.assign({}, state, {
        is_saving: true
      })
    case types.ENABLE_SUBMIT:
      return Object.assign({}, state, {
        is_saving: false
      })
    case types.FETCH_INITIAL_DATA:
      let current_price_plan = {};
      const price_plans = action.data.price_plans;
      const current_price_plan_code = action.data.current_price_plan_code;
      for (let i = 0; i < price_plans.length; i++) {
        if (price_plans[i].code == current_price_plan_code) {
          current_price_plan = price_plans[i]
          break;
        }
      }
      return Object.assign({}, state, action.data, {
        current_price_plan,
      })
    case types.SELECT_PRICE_PLAN:
      return Object.assign({}, state, {
        selected_price_plan: action.plan
      })

    default:
      return state;
  }
}
