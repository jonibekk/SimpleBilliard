import * as ActionTypes from "~/saved_item/constants/ActionTypes";
import {Post} from "~/common/constants/Model";

const initialState = {
  search_result: {
    data: [],
    counts: 0,
    paging: {
      next: ''
    }
  },
  search_conditions: {
    type: ""
  },
  loading: false,
  loading_more: false,
  is_mobile_app: false,
}


export default function saved_item(state = initialState, action) {
  let search_conditions = state.search_conditions
  let search_result = state.search_result

  switch (action.type) {
    case ActionTypes.FETCH_INITIAL_DATA:
      return Object.assign({}, state, action.data, {
        loading: false
      })

    case ActionTypes.SEARCH:
      search_result = {
        data: action.search_result.data,
        paging: action.search_result.paging,
        counts: state.search_result.counts
      }
      return Object.assign({}, state, {
        search_result,
        search_conditions: action.search_conditions,
        loading: false
      })

    case ActionTypes.FETCH_MORE:
      search_result = {
        data: [...state.search_result.data, ...action.search_result.data],
        paging: action.search_result.paging,
        counts: state.search_result.counts
      }
      return Object.assign({}, state, {
        search_result,
        loading_more: false
      })

    case ActionTypes.REMOVE:
      search_result.data = updateSavedItemsByUnsaving(search_result.data, action.item.id);
      search_result.counts.all--;
      if (action.item.type == Post.TYPE.ACTION) {
        search_result.counts.action--;
      } else {
        search_result.counts.normal--;
      }
      return Object.assign({}, state, {
        search_result
      })
    case ActionTypes.LOADING:
      return Object.assign({}, state, {
        loading: true
      })

    case ActionTypes.LOADING_MORE:
      return Object.assign({}, state, {
        loading_more: true
      })
    case ActionTypes.SET_UA_INFO:
      return Object.assign({}, state, {
        is_mobile_app: action.is_mobile_app
      })
    default:
      return state;
  }
}

/**
 * フォローアクションによってゴールリストを更新
 *
 * @param data
 * @param goal_id
 * @param add_flg
 * @returns {*}
 */
export function updateSavedItemsByUnsaving(data, target_id) {
  return data.filter((v) => {
    if (v.id == target_id) {
      return false;
    }
    return true
  })
}
